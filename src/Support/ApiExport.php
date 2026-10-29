<?php

namespace SteelAnts\LaravelBoilerplate\Support;

use Illuminate\Support\Str;

/**
 * Builds API client collections from the routes of the system API page.
 * A token is never exported, requests only reference a variable for it.
 */
class ApiExport
{
    public static function openApi(array $routes, string $name, string $baseUrl): array
    {
        $paths = [];

        foreach ($routes as $route) {
            $method = strtolower($route['Method']);
            $comments = collect($route['Parameters'])->pluck('comment', 'name');
            $description = trim($route['Description']);

            $operation = [
                'summary'   => Str::before($description, "\n") ?: $route['Uri'],
                'responses' => ['200' => ['description' => 'OK']],
            ];
            if ($description !== '') {
                $operation['description'] = $description;
            }

            $parameters = array_map(fn ($param) => [
                'name'     => $param,
                'in'       => 'path',
                'required' => true,
                'schema'   => ['type' => 'string'],
            ], $route['RouteParams']);

            if (self::sendsQuery($route['Method'])) {
                foreach ($route['Fields'] as $field => $definition) {
                    $parameters[] = array_filter([
                        'name'        => $field,
                        'in'          => 'query',
                        'required'    => self::isRequired($definition),
                        'description' => $comments[$field] ?? null,
                        'schema'      => self::schema($definition),
                    ], fn ($value) => $value !== null);
                }
            } elseif ($route['Fields']) {
                $operation['requestBody'] = [
                    'content' => ['application/json' => ['schema' => self::objectSchema($route['Fields'], $comments->all())]],
                ];
            }

            if ($parameters) {
                $operation['parameters'] = $parameters;
            }
            if ($route['RequiresToken']) {
                $operation['security'] = [['bearerAuth' => []]];
            }

            $paths['/' . self::uri($route['Uri'], '{%s}')][$method] = $operation;
        }

        return [
            'openapi'    => '3.0.3',
            'info'       => ['title' => $name, 'version' => '1.0.0'],
            'servers'    => [['url' => $baseUrl]],
            'paths'      => $paths,
            'components' => ['securitySchemes' => ['bearerAuth' => ['type' => 'http', 'scheme' => 'bearer']]],
        ];
    }

    public static function insomnia(array $routes, string $name, string $baseUrl): array
    {
        $resources = [
            ['_id' => 'wrk_api', '_type' => 'workspace', 'name' => $name, 'scope' => 'collection'],
            ['_id' => 'env_api', '_type' => 'environment', 'parentId' => 'wrk_api', 'name' => 'Base Environment', 'data' => ['base_url' => $baseUrl, 'token' => '']],
        ];

        foreach ($routes as $index => $route) {
            $resources[] = [
                '_id'            => 'req_api_' . $index,
                '_type'          => 'request',
                'parentId'       => 'wrk_api',
                'name'           => $route['Method'] . ' ' . $route['Uri'],
                'description'    => $route['Description'],
                'method'         => $route['Method'],
                'url'            => '{{ _.base_url }}/' . self::uri($route['Uri'], ':%s'),
                'pathParameters' => self::emptyParams($route['RouteParams']),
                'parameters'     => self::sendsQuery($route['Method']) ? self::emptyParams(array_keys($route['Fields'])) : [],
                'body'           => self::jsonBody($route, 'mimeType'),
                'headers'        => [['name' => 'Accept', 'value' => 'application/json']],
                'authentication' => $route['RequiresToken'] ? ['type' => 'bearer', 'token' => '{{ _.token }}'] : (object) [],
            ];
        }

        return [
            '_type'           => 'export',
            '__export_format' => 4,
            '__export_date'   => now()->toIso8601String(),
            '__export_source' => 'ggj.api-export',
            'resources'       => $resources,
        ];
    }

    public static function restfox(array $routes, string $name, string $baseUrl): array
    {
        $collection = [[
            '_id'         => 'grp_api',
            '_type'       => 'request_group',
            'name'        => $name,
            'parentId'    => null,
            'workspaceId' => '',
            'environment' => ['base_url' => $baseUrl, 'token' => ''],
        ]];

        foreach ($routes as $index => $route) {
            $collection[] = [
                '_id'            => 'req_api_' . $index,
                '_type'          => 'request',
                'name'           => $route['Method'] . ' ' . $route['Uri'],
                'description'    => $route['Description'],
                'method'         => $route['Method'],
                'url'            => '{{base_url}}/' . self::uri($route['Uri'], '{%s}'),
                'pathParameters' => self::emptyParams($route['RouteParams']),
                'parameters'     => self::sendsQuery($route['Method']) ? self::emptyParams(array_keys($route['Fields'])) : [],
                'body'           => self::jsonBody($route, 'mimeType') ?: ['mimeType' => 'No Body'],
                'headers'        => [['name' => 'Accept', 'value' => 'application/json']],
                'authentication' => $route['RequiresToken'] ? ['type' => 'bearer', 'token' => '{{token}}'] : ['type' => 'No Auth'],
                'parentId'       => 'grp_api',
                'workspaceId'    => '',
                'sortOrder'      => $index,
            ];
        }

        return ['exportedFrom' => 'Restfox-1.0.0', 'collection' => $collection];
    }

    private static function sendsQuery(string $method): bool
    {
        return in_array($method, ['GET', 'HEAD', 'DELETE'], true);
    }

    // route uri with path parameters in the client's syntax, optional {param?} included
    private static function uri(string $uri, string $parameterFormat): string
    {
        return preg_replace_callback('~\{(\w+)\??\}~', fn ($match) => sprintf($parameterFormat, $match[1]), $uri);
    }

    private static function emptyParams(array $names): array
    {
        return array_map(fn ($name) => ['name' => $name, 'value' => ''], array_values($names));
    }

    private static function jsonBody(array $route, string $mimeKey): array
    {
        if (self::sendsQuery($route['Method']) || !$route['Fields']) {
            return [];
        }

        $example = array_map(fn ($definition) => self::example($definition), $route['Fields']);

        return [$mimeKey => 'application/json', 'text' => json_encode($example, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)];
    }

    private static function isRequired(array $definition): bool
    {
        return in_array('required', explode('|', $definition['rules']), true);
    }

    private static function schema(array $definition): array
    {
        $rules = explode('|', $definition['rules']);

        return match ($definition['input']) {
            'number'   => ['type' => in_array('integer', $rules, true) ? 'integer' : 'number'],
            'checkbox' => ['type' => 'boolean'],
            'date'     => ['type' => 'string', 'format' => 'date'],
            'email'    => ['type' => 'string', 'format' => 'email'],
            'select'   => ['type' => 'string', 'enum' => $definition['options']],
            'array'    => ['type' => 'array', 'items' => ['type' => 'string']],
            default    => ['type' => 'string'],
        };
    }

    private static function objectSchema(array $fields, array $comments): array
    {
        $properties = [];
        foreach ($fields as $field => $definition) {
            $properties[$field] = self::schema($definition) + array_filter(['description' => $comments[$field] ?? null]);
        }
        $required = array_keys(array_filter($fields, fn ($definition) => self::isRequired($definition)));

        return array_filter(['type' => 'object', 'properties' => $properties, 'required' => $required]);
    }

    private static function example(array $definition): mixed
    {
        return match ($definition['input']) {
            'number'   => 0,
            'checkbox' => false,
            'array'    => [],
            'select'   => $definition['options'][0] ?? '',
            default    => '',
        };
    }
}
