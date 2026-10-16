<?php

/**
 * Najde ploché bloky akcí ve všech prefab DataTable komponentách.
 * Klíče bloku jdou vždy v pořadí type, action, parameters, text, actionClass, iconClass,
 * proto blok končí u dalšího 'type' nebo na konci metody.
 *
 * @return array<int, array{file: string, action: string, actionClass: ?string, iconClass: ?string}>
 */
function prefabDataTableActions(): array
{
    $root = dirname(__DIR__, 3) . '/prefabs/app/Livewire';
    $actions = [];

    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS));
    foreach ($files as $file) {
        if (!preg_match('/DataTable.*\.php$/', $file->getFilename())) {
            continue;
        }

        $code = file_get_contents($file->getPathname());
        preg_match_all("/'action'\s*=>\s*'(\w+)'(.*?)(?='type'\s*=>|\n    \}\n)/s", $code, $blocks, PREG_SET_ORDER);

        foreach ($blocks as $block) {
            preg_match("/'actionClass'\s*=>\s*'([^']*)'/", $block[2], $actionClass);
            preg_match("/'iconClass'\s*=>\s*'([^']*)'/", $block[2], $iconClass);

            $actions[] = [
                'file'        => str_replace($root . DIRECTORY_SEPARATOR, '', $file->getPathname()),
                'action'      => $block[1],
                'actionClass' => $actionClass[1] ?? null,
                'iconClass'   => $iconClass[1] ?? null,
            ];
        }
    }

    return $actions;
}

function isDestructivePrefabAction(array $action): bool
{
    return preg_match('/remove|delete|destroy|stop|logout|revoke/i', $action['action']) === 1
        || str_contains((string) $action['iconClass'], 'fa-trash');
}

describe('prefab DataTable destructive actions', function () {
    it('finds at least as many actions as the prefabs have today', function () {
        // Dnes: Activity 0, Job 4, Session 1, Subscription 0, User 2 (+ případné další). Pojistka proti prázdnému zelenému testu.
        expect(count(prefabDataTableActions()))->toBeGreaterThanOrEqual(7);
        expect(count(array_filter(prefabDataTableActions(), 'isDestructivePrefabAction')))->toBeGreaterThanOrEqual(3);
    });

    it('marks every destructive action with fa-trash, text-danger action class and red icon', function () {
        $problems = [];

        foreach (array_filter(prefabDataTableActions(), 'isDestructivePrefabAction') as $action) {
            $label = $action['file'] . ' :: ' . $action['action'];

            if (!str_contains((string) $action['iconClass'], 'fa-trash')) {
                $problems[] = $label . ' - iconClass lacks fa-trash';
            }
            if (!str_contains((string) $action['actionClass'], 'text-danger')) {
                $problems[] = $label . ' - actionClass lacks text-danger';
            }
            if (!str_contains((string) $action['iconClass'], 'text-danger')) {
                $problems[] = $label . ' - iconClass lacks text-danger';
            }
        }

        expect($problems)->toBe([]);
    });
});
