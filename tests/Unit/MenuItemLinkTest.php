<?php

namespace SteelAnts\LaravelBoilerplate\Tests\Unit;

use Illuminate\Support\Facades\Route;
use SteelAnts\LaravelBoilerplate\Support\MenuItemLink;
use Tests\TestCase;

class MenuItemLinkTest extends TestCase
{
    public function test_is_use_returns_true_for_current_route(): void
    {
        Route::middleware('web')->get('/dashboard', fn () => 'ok')->name('dashboard');
        $this->refreshRouteLookups();

        $item = new MenuItemLink('Dashboard', 'dashboard', 'dashboard');

        $this->get('/dashboard');

        $this->assertTrue($item->isUse());
    }

    public function test_is_use_returns_true_for_child_route(): void
    {
        Route::middleware('web')->get('/dashboard', fn () => 'ok')->name('dashboard');
        Route::middleware('web')->get('/dashboard/settings', fn () => 'ok')->name('dashboard.settings');
        $this->refreshRouteLookups();

        $item = new MenuItemLink('Dashboard', 'dashboard', 'dashboard');

        $this->get('/dashboard/settings');

        $this->assertTrue($item->isUse());
    }

    public function test_is_active_requires_matching_route_parameters_and_query(): void
    {
        Route::middleware('web')->get('/users/{user}', fn ($user) => "User {$user}")->name('users.show');
        $this->refreshRouteLookups();

        $item = new MenuItemLink(
            'User detail',
            'user_show',
            'users.show',
            parameters: ['user' => 5],
            options: ['query' => ['tab' => 'profile']]
        );

        $this->get('/users/5?tab=profile');

        $this->assertTrue($item->isActive());
    }

    public function test_is_active_returns_false_when_route_parameter_differs(): void
    {
        Route::middleware('web')->get('/users/{user}', fn ($user) => "User {$user}")->name('users.show');
        $this->refreshRouteLookups();

        $item = new MenuItemLink(
            'User detail',
            'user_show',
            'users.show',
            parameters: ['user' => 5]
        );

        $this->get('/users/6');

        $this->assertFalse($item->isActive());
    }

    public function test_is_active_returns_false_for_unexpected_query_parameters(): void
    {
        Route::middleware('web')->get('/users/{user}', fn ($user) => "User {$user}")->name('users.show');
        $this->refreshRouteLookups();

        $item = new MenuItemLink(
            'User detail',
            'user_show',
            'users.show',
            parameters: ['user' => 5]
        );

        $this->get('/users/5?extra=yes');

        $this->assertFalse($item->isActive());
    }

    public function test_is_active_returns_false_when_expected_query_value_differs(): void
    {
        Route::middleware('web')->get('/users/{user}', fn ($user) => "User {$user}")->name('users.show');
        $this->refreshRouteLookups();

        $item = new MenuItemLink(
            'User detail',
            'user_show',
            'users.show',
            parameters: ['user' => 5],
            options: ['query' => ['tab' => 'profile']]
        );

        $this->get('/users/5?tab=activity');

        $this->assertFalse($item->isActive());
    }

    public function test_is_active_returns_true_with_matching_query_only(): void
    {
        Route::middleware('web')->get('/tasks', fn () => 'ok')->name('tasks.index');
        $this->refreshRouteLookups();

        $item = new MenuItemLink(
            'Tasks',
            'tasks',
            'tasks.index',
            options: ['query' => ['milestoe' => 1]]
        );

        $this->get('/tasks?milestoe=1');

        $this->assertTrue($item->isActive());
    }

    private function refreshRouteLookups(): void
    {
        Route::getRoutes()->refreshNameLookups();
    }
}
