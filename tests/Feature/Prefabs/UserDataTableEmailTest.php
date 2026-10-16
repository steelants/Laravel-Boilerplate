<?php

use App\Livewire\User\DataTable;

beforeEach(function () {
    // Prefab žije v namespace App\, appka ho dostane až po instalaci, proto ho načítáme ručně.
    require_once dirname(__DIR__, 3) . '/prefabs/app/Livewire/User/DataTable.php';
});

describe('prefab User DataTable e-mail column', function () {
    it('shows a check icon next to a verified e-mail', function () {
        $html = (new DataTable())->renderColumnEmail('jane@example.com', (object) ['email_verified_at' => now()]);

        expect($html)
            ->toContain('jane@example.com')
            ->toContain('fa-check-circle')
            ->toContain('text-success')
            ->toContain('title="E-mail verified"');
    });

    it('shows no icon next to an unverified e-mail', function () {
        $html = (new DataTable())->renderColumnEmail('jane@example.com', (object) ['email_verified_at' => null]);

        expect($html)->toBe('jane@example.com');
    });

    it('escapes the e-mail value in both cases', function () {
        $table = new DataTable();
        $evil = '<script>alert(1)</script>@example.com';

        expect($table->renderColumnEmail($evil, (object) ['email_verified_at' => null]))
            ->not->toContain('<script>')
            ->toContain('&lt;script&gt;');

        expect($table->renderColumnEmail($evil, (object) ['email_verified_at' => now()]))
            ->not->toContain('<script>')
            ->toContain('&lt;script&gt;');
    });

    it('translates the verified tooltip into Czech', function () {
        app()->setLocale('cs');

        $html = (new DataTable())->renderColumnEmail('jane@example.com', (object) ['email_verified_at' => now()]);

        expect($html)->toContain('title="E-mail ověřen"');
    });
});
