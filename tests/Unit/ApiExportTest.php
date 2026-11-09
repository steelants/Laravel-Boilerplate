<?php

use SteelAnts\LaravelBoilerplate\Support\ApiExport;

function apiExportRoutes(): array
{
    return [
        [
            'Method'        => 'GET',
            'Uri'           => 'api/items/{item}',
            'RouteParams'   => ['item'],
            'Description'   => "Item detail\nMore text",
            'Parameters'    => [['name' => 'from', 'rules' => 'nullable|date', 'comment' => 'Created since']],
            'Returns'       => 'array',
            'Fields'        => ['from' => ['rules' => 'nullable|date', 'input' => 'date', 'options' => []]],
            'RequiresToken' => true,
        ],
        [
            'Method'        => 'POST',
            'Uri'           => 'api/items',
            'RouteParams'   => [],
            'Description'   => '',
            'Parameters'    => [],
            'Returns'       => 'array',
            'Fields'        => ['name' => ['rules' => 'required|string', 'input' => 'text', 'options' => []]],
            'RequiresToken' => false,
        ],
    ];
}

it('builds an OpenAPI spec', function () {
    $spec = ApiExport::openApi(apiExportRoutes(), 'Test API', 'https://example.test');

    expect($spec['openapi'])->toBe('3.0.3')
        ->and($spec['servers'][0]['url'])->toBe('https://example.test');

    $get = $spec['paths']['/api/items/{item}']['get'];
    expect($get['summary'])->toBe('Item detail')
        ->and($get['security'])->toBe([['bearerAuth' => []]])
        ->and($get['parameters'][0]['in'])->toBe('path')
        ->and($get['parameters'][1])->toMatchArray(['name' => 'from', 'in' => 'query', 'required' => false, 'description' => 'Created since']);

    $post = $spec['paths']['/api/items']['post'];
    expect($post)->not->toHaveKey('security')
        ->and($post['requestBody']['content']['application/json']['schema']['required'])->toBe(['name']);
});

it('builds an Insomnia export', function () {
    $export = ApiExport::insomnia(apiExportRoutes(), 'Test API', 'https://example.test');
    $requests = array_values(array_filter($export['resources'], fn ($item) => $item['_type'] === 'request'));

    expect($export['__export_format'])->toBe(4)
        ->and($requests[0]['url'])->toBe('{{ _.base_url }}/api/items/:item')
        ->and($requests[0]['authentication'])->toBe(['type' => 'bearer', 'token' => '{{ _.token }}'])
        ->and($requests[1]['body']['mimeType'])->toBe('application/json');
});

it('builds a Restfox export', function () {
    $export = ApiExport::restfox(apiExportRoutes(), 'Test API', 'https://example.test');
    $requests = array_values(array_filter($export['collection'], fn ($item) => $item['_type'] === 'request'));

    expect($export['exportedFrom'])->toBe('Restfox-1.0.0')
        ->and($requests[0]['url'])->toBe('{{base_url}}/api/items/{item}')
        ->and($requests[0]['authentication'])->toBe(['type' => 'bearer', 'token' => '{{token}}'])
        ->and($requests[1]['authentication'])->toBe(['type' => 'No Auth']);
});
