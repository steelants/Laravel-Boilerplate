<?php

namespace SteelAnts\LaravelBoilerplate\Tests\Unit;

use Illuminate\Support\Facades\Route;
use SteelAnts\LaravelBoilerplate\Support\MenuBuilder;
use SteelAnts\LaravelBoilerplate\Support\MenuItemLink;
use SteelAnts\LaravelBoilerplate\Tests\TestCase;

class MenuItemLinkTest extends TestCase
{
    public function test_is_use_returns_true_for_current_route(): void
    {
        Route::get('/dashboard', fn () => 'ok')->name('dashboard');
        $this->refreshRouteLookups();

        $item = new MenuItemLink('Dashboard', 'dashboard', 'dashboard');

        $this->get('/dashboard');

        $this->assertTrue($item->isUse());
    }

    public function test_is_use_returns_true_for_child_route(): void
    {
        Route::get('/dashboard', fn () => 'ok')->name('dashboard');
        Route::get('/dashboard/settings', fn () => 'ok')->name('dashboard.settings');
        $this->refreshRouteLookups();

        $item = new MenuItemLink('Dashboard', 'dashboard', 'dashboard');

        $this->get('/dashboard/settings');

        $this->assertTrue($item->isUse());
    }

    public function test_is_use_returns_true_for_child_of_an_index_route(): void
    {
        Route::get('/tasks', fn () => 'ok')->name('tasks.index');
        Route::get('/tasks/{task}/edit', fn ($task) => 'ok')->name('tasks.edit');
        $this->refreshRouteLookups();

        $item = new MenuItemLink('Tasks', 'tasks', 'tasks.index');

        $this->get('/tasks/3/edit');

        $this->assertTrue($item->isUse());
        $this->assertFalse($item->isActive());
    }

    public function test_is_use_does_not_leak_over_a_route_name_boundary(): void
    {
        Route::get('/users', fn () => 'ok')->name('user.index');
        Route::get('/user-groups', fn () => 'ok')->name('userGroup.index');
        $this->refreshRouteLookups();

        $item = new MenuItemLink('Users', 'users', 'user.index');

        $this->get('/user-groups');

        $this->assertFalse($item->isUse());
    }

    public function test_is_active_requires_matching_route_parameters_and_query(): void
    {
        Route::get('/users/{user}', fn ($user) => "User {$user}")->name('users.show');
        $this->refreshRouteLookups();

        $item = new MenuItemLink(
            'User detail',
            'user_show',
            'users.show',
            parameters: ['user' => 5, 'tab' => 'profile']
        );

        $this->get('/users/5?tab=profile');

        $this->assertTrue($item->isActive());
    }

    public function test_is_active_returns_false_when_route_parameter_differs(): void
    {
        Route::get('/users/{user}', fn ($user) => "User {$user}")->name('users.show');
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

    public function test_is_active_ignores_query_parameters_no_item_declares(): void
    {
        Route::get('/users/{user}', fn ($user) => "User {$user}")->name('users.show');
        $this->refreshRouteLookups();

        $item = new MenuItemLink(
            'User detail',
            'user_show',
            'users.show',
            parameters: ['user' => 5]
        );

        $this->get('/users/5?extra=yes');

        $this->assertTrue($item->isActive());
    }

    public function test_is_active_returns_false_when_expected_query_value_differs(): void
    {
        Route::get('/users/{user}', fn ($user) => "User {$user}")->name('users.show');
        $this->refreshRouteLookups();

        $item = new MenuItemLink(
            'User detail',
            'user_show',
            'users.show',
            parameters: ['user' => 5, 'tab' => 'profile']
        );

        $this->get('/users/5?tab=activity');

        $this->assertFalse($item->isActive());
    }

    public function test_is_active_returns_true_with_matching_query_only(): void
    {
        Route::get('/tasks', fn () => 'ok')->name('tasks.index');
        $this->refreshRouteLookups();

        $item = new MenuItemLink(
            'Tasks',
            'tasks',
            'tasks.index',
            parameters: ['milestone' => 1]
        );

        $this->get('/tasks?milestone=1');

        $this->assertTrue($item->isActive());
    }

    public function test_is_active_survives_paging_and_sorting_in_the_url(): void
    {
        Route::get('/tasks', fn () => 'ok')->name('tasks.index');
        $this->refreshRouteLookups();

        $item = new MenuItemLink('Filter', 'filter_9', 'tasks.index', parameters: ['filter' => 9]);

        $this->get('/tasks?filter=9&page=2&sortBy=name');

        $this->assertTrue($item->isActive());
    }

    public function test_only_the_selected_sibling_filter_is_highlighted(): void
    {
        Route::get('/tasks', fn () => 'ok')->name('tasks.index');
        $this->refreshRouteLookups();

        $menu = new MenuBuilder;
        $all = $menu->addRoute('All', 'all', 'tasks.index');
        $selected = $menu->addRoute('Filter 9', 'filter_9', 'tasks.index', parameters: ['filter' => 9]);
        $other = $menu->addRoute('Filter 10', 'filter_10', 'tasks.index', parameters: ['filter' => 10]);

        $this->get('/tasks?filter=9&page=2');

        $this->assertTrue($selected->isActive());
        $this->assertTrue($selected->isUse());

        $this->assertFalse($other->isActive());
        $this->assertFalse($other->isUse());

        $this->assertFalse($all->isActive());
        $this->assertFalse($all->isUse());
    }

    public function test_item_without_parameters_is_active_on_the_plain_url(): void
    {
        Route::get('/tasks', fn () => 'ok')->name('tasks.index');
        $this->refreshRouteLookups();

        $menu = new MenuBuilder;
        $all = $menu->addRoute('All', 'all', 'tasks.index');
        $menu->addRoute('Filter 9', 'filter_9', 'tasks.index', parameters: ['filter' => 9]);

        $this->get('/tasks?page=2');

        $this->assertTrue($all->isActive());
    }

    public function test_sibling_parameters_of_another_route_are_not_compared(): void
    {
        Route::get('/tasks', fn () => 'ok')->name('tasks.index');
        Route::get('/users', fn () => 'ok')->name('users.index');
        $this->refreshRouteLookups();

        $menu = new MenuBuilder;
        $users = $menu->addRoute('Users', 'users', 'users.index');
        $menu->addRoute('Filter 9', 'filter_9', 'tasks.index', parameters: ['filter' => 9]);

        $this->get('/users?filter=9');

        $this->assertTrue($users->isActive());
    }

    public function test_filter_item_is_not_used_on_a_detail_page(): void
    {
        Route::get('/tasks', fn () => 'ok')->name('tasks.index');
        Route::get('/tasks/{task}/edit', fn ($task) => 'ok')->name('tasks.edit');
        $this->refreshRouteLookups();

        $menu = new MenuBuilder;
        $all = $menu->addRoute('All', 'all', 'tasks.index');
        $filter = $menu->addRoute('Filter 9', 'filter_9', 'tasks.index', parameters: ['filter' => 9]);

        $this->get('/tasks/3/edit');

        $this->assertTrue($all->isUse());
        $this->assertFalse($filter->isUse());
    }

    public function test_item_is_neither_active_nor_used_without_a_current_route(): void
    {
        Route::get('/tasks', fn () => 'ok')->name('tasks.index');
        $this->refreshRouteLookups();

        $item = new MenuItemLink('Tasks', 'tasks', 'tasks.index');

        $this->assertFalse($item->isActive());
        $this->assertFalse($item->isUse());
    }

    private function refreshRouteLookups(): void
    {
        Route::getRoutes()->refreshNameLookups();
    }
}
