<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use SteelAnts\LaravelBoilerplate\Support\ApiExport;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use ReflectionClass;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionIntersectionType;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionUnionType;
use Stringable;
use Throwable;

class ApiController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('system.api.index', [
            'layout' => config('boilerplate.layouts.system'),
            'routes' => $this->apiRoutes(),
        ]);
    }

    /**
     * Collection for API clients: openapi | insomnia | restfox. Never contains a token, only a variable for it.
     */
    public function export(string $format): Response
    {
        $routes = $this->apiRoutes();
        $name = config('app.name') . ' API';

        [$data, $filename] = match ($format) {
            'openapi'  => [ApiExport::openApi($routes, $name, url('/')), 'openapi.json'],
            'insomnia' => [ApiExport::insomnia($routes, $name, url('/')), 'insomnia.json'],
            'restfox'  => [ApiExport::restfox($routes, $name, url('/')), 'restfox.json'],
        };

        return response(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), 200, [
            'Content-Type'        => 'application/json',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function apiRoutes(): array
    {
        $routes = [];
        $routesCollection = Route::getRoutes();

        foreach ($routesCollection as $route) {
            if (!str_starts_with($route->uri(), 'api')) {
                continue;
            }

            $reflectionMethod = null;
            if ($route->getAction('uses') instanceof Closure) {
                $reflectionMethod = new ReflectionFunction($route->getAction('uses'));
            } elseif (str_contains($route->getActionName(), '@')) {
                [
                    $class,
                    $method,
                ] = explode('@', $route->getActionName());
                $reflectionClass = new ReflectionClass($class);
                $reflectionMethod = $reflectionClass->getMethod($method);
            }

            $fields = $reflectionMethod != null ? $this->formRequestFields($reflectionMethod) : [];
            $phpDocParameters = $reflectionMethod != null ? $this->phpDocsParameters($reflectionMethod) : [];

            $routes[] = [
                'Method'        => $route->methods()[0],
                'Uri'           => $route->uri(),
                'RouteParams'   => $route->parameterNames(),
                'Description'   => ($reflectionMethod != null ? $this->phpDocsDescription($reflectionMethod) : ''),
                'Parameters'    => $this->mergeParameters($fields, $phpDocParameters),
                'Returns'       => ($reflectionMethod != null ? $this->returnTypeName($reflectionMethod) : 'NULL'),
                'Fields'        => $fields,
                'RequiresToken' => collect($route->gatherMiddleware())->contains(fn ($middleware) => is_string($middleware) && str_starts_with($middleware, 'auth')),
            ];
        }

        return $routes;
    }

    /**
     * Top-level fields of the FormRequest the action expects: [name => ['rules' => string, 'input' => string, 'options' => array]].
     * Validation done inline with $request->validate() cannot be detected without running the action.
     */
    private function formRequestFields(ReflectionFunctionAbstract $method): array
    {
        foreach ($method->getParameters() as $parameter) {
            $type = $parameter->getType();
            if (!$type instanceof ReflectionNamedType || $type->isBuiltin() || !is_subclass_of($type->getName(), FormRequest::class)) {
                continue;
            }

            try {
                $class = $type->getName();
                $request = new $class;
                $rules = method_exists($request, 'rules') ? app()->call([$request, 'rules']) : [];
            } catch (Throwable) {
                return [];
            }

            $fields = [];
            foreach ($rules as $name => $fieldRules) {
                if (str_contains($name, '.')) {
                    continue;
                }

                $ruleList = array_map(
                    fn ($rule) => is_string($rule) || $rule instanceof Stringable || method_exists($rule, '__toString') ? (string) $rule : class_basename($rule),
                    is_string($fieldRules) ? explode('|', $fieldRules) : (array) $fieldRules
                );
                $fields[$name] = ['rules' => implode('|', $ruleList)] + $this->inputType($ruleList);
            }

            return $fields;
        }

        return [];
    }

    /**
     * One row per name: FormRequest rules are more precise than the PHPDoc type,
     * PHPDoc adds the comment; names only in PHPDoc are kept with their PHPDoc type.
     *
     * @return array<int, array{name: string, rules: string, comment: string}>
     */
    private function mergeParameters(array $fields, array $phpDocParameters): array
    {
        $comments = collect($phpDocParameters)->pluck('comment', 'name');
        $parameters = [];

        foreach ($fields as $name => $field) {
            $parameters[$name] = ['name' => $name, 'rules' => $field['rules'], 'comment' => (string) ($comments[$name] ?? '')];
        }
        foreach ($phpDocParameters as $parameter) {
            $parameters[$parameter['name']] ??= ['name' => $parameter['name'], 'rules' => $parameter['type'], 'comment' => (string) $parameter['comment']];
        }

        return array_values($parameters);
    }

    /**
     * Input type of a field, by its validation rules: ['input' => text|number|checkbox|date|email|select|array, 'options' => [...]].
     */
    private function inputType(array $rules): array
    {
        foreach ($rules as $rule) {
            if (str_starts_with($rule, 'in:')) {
                return ['input' => 'select', 'options' => array_map(fn ($option) => trim($option, '"\''), explode(',', substr($rule, 3)))];
            }
        }

        $input = match (true) {
            (bool) array_intersect($rules, ['integer', 'numeric']) => 'number',
            in_array('boolean', $rules, true)                      => 'checkbox',
            (bool) array_intersect($rules, ['date', 'date_format:Y-m-d']) => 'date',
            in_array('email', $rules, true)                        => 'email',
            in_array('array', $rules, true)                        => 'array',
            default                                                => 'text',
        };

        return ['input' => $input, 'options' => []];
    }

    /**
     * Vrati nazev navratoveho typu metody jako citelny string.
     * Union a intersection typy nemaji getName(), proto je skladame z dilcich typu.
     */
    private function returnTypeName(ReflectionFunctionAbstract $method): string
    {
        $type = $method->getReturnType();

        return match (true) {
            $type instanceof ReflectionNamedType        => $type->getName(),
            $type instanceof ReflectionUnionType        => $this->joinTypeNames($type->getTypes(), '|'),
            $type instanceof ReflectionIntersectionType => $this->joinTypeNames($type->getTypes(), '&'),
            default                                     => 'NULL',
        };
    }

    /**
     * Spoji nazvy dilcich typu. Nelze pouzit pluck('name') - ReflectionNamedType
     * nema verejnou property $name, nazev vraci pouze metoda getName().
     *
     * @param  array<int, \ReflectionType>  $types
     */
    private function joinTypeNames(array $types, string $separator): string
    {
        $names = array_map(
            fn ($type) => $type instanceof ReflectionNamedType ? $type->getName() : 'UNKNOWN',
            $types
        );

        return implode($separator, $names);
    }

    private function phpDocsParameters(ReflectionFunctionAbstract $method): array
    {
        // Retrieve the full PhpDoc comment block
        $doc = $method->getDocComment();

        // Metoda bez PHPDoc bloku vraci false, coz nelze predat do explode()
        if ($doc === false) {
            return [];
        }

        // Trim each line from space and star chars
        $lines = array_map(function ($line) {
            return trim($line, ' *');
        }, explode("\n", $doc));

        // Retain lines that start with an @
        $lines = array_filter($lines, function ($line) {
            return strpos($line, '@param') === 0;
        });

        $args = [];

        // Push each value in the corresponding @param array
        foreach ($lines as $line) {
            [
                $null,
                $type,
                $name,
                $comment,
            ] = explode(' ', $line, 4);

            $args[] = [
                'type'    => $type,
                'name'    => $name,
                'comment' => $comment,
            ];
        }

        return $args;
    }

    private function phpDocsDescription(ReflectionFunctionAbstract $method): string
    {
        $doc = $method->getDocComment();

        // Metoda bez PHPDoc bloku vraci false, coz nelze predat do explode()
        if ($doc === false) {
            return '';
        }

        $lines = [];

        foreach (explode("\n", $doc) as $i => $line) {
            $trimedLine = trim(trim($line, ' *'), '/');

            if (str_starts_with($trimedLine, '@')) {
                break;
            }

            $lines[$i] = $trimedLine;
        }

        return implode("\n", $lines);
    }
}
