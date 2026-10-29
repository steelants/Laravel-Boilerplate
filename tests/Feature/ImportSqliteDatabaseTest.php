<?php

use Illuminate\Support\Facades\Artisan;

it('is registered', function () {
    expect(Artisan::all())->toHaveKey('db:import-sqlite');
});

it('refuses a target that is not PostgreSQL or MySQL', function () {
    $file = tempnam(sys_get_temp_dir(), 'import-test-');
    file_put_contents($file, 'x');

    $this->artisan('db:import-sqlite', ['path' => $file, '--target' => 'testing', '--force' => true])
        ->expectsOutputToContain('neither PostgreSQL nor MySQL')
        ->assertFailed();

    unlink($file);
});
