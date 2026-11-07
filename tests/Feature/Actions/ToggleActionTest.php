<?php

use SteelAnts\LaravelBoilerplate\RenderCasts\ToggleAction;

// Bool sloupec přepínaný akcí ze sady DataTable: is_active -> runAction(['toggleIsActive', klíč]).

test('the toggle render cast calls the toggle action derived from the column', function () {
    $html = app(ToggleAction::class)->render('is_active', true, ['id' => 5]);

    expect($html)->toContain('wire:click="runAction(')
        ->toContain(e(json_encode(['toggleIsActive', '5'])));
});

test('the toggle render cast escapes a string key', function () {
    $html = app(ToggleAction::class)->render('totp_force', false, ['id' => "a'b"]);

    expect($html)->not->toContain("a'b'");
});
