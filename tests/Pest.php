<?php

use SteelAnts\LaravelBoilerplate\Tests\TestCase;

uses(TestCase::class)->in('Feature');

// Testy v tests/App se publikuji do aplikace a pouzivaji jeji Tests\TestCase.
if (!class_exists(\Tests\TestCase::class)) {
    class_alias(TestCase::class, \Tests\TestCase::class);
}
