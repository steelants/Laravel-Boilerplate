<?php

namespace SteelAnts\LaravelBoilerplate\Tests\Unit;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use SteelAnts\LaravelBoilerplate\Facades\Menu;
use SteelAnts\LaravelBoilerplate\Support\MenuBuilder;
use SteelAnts\LaravelBoilerplate\Support\MenuItem;
use SteelAnts\LaravelBoilerplate\Support\MenuItemAction;
use SteelAnts\LaravelBoilerplate\Support\MenuItemLink;
use SteelAnts\LaravelBoilerplate\Support\MenuSet;
use SteelAnts\LaravelBoilerplate\Tests\TestCase;

class MenuDefinitionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Route::get('/home', fn () => 'ok')->name('home');
        Route::get('/tasks', fn () => 'ok')->name('tasks.index');
        Route::get('/projects/{project}', fn ($project) => 'ok')->name('projects.show');
        app('router')->getRoutes()->refreshNameLookups();
    }

    public function test_define_builds_the_same_items_as_the_array_api(): void
    {
        $menu = (new MenuBuilder)->define([
            MenuItem::make('home')->label('Home')->icon('fas fa-home')->route('home'),
            MenuItem::make('tasks')->label('Tasks')->route('tasks.index', ['filter' => 9]),
            MenuItem::make('delete')->label('Delete')->action('wire:click="delete"')->confirm('Sure?'),
            MenuItem::make('system')->label('System')->as('toggle'),
        ]);

        [$home, $tasks, $delete, $system] = $menu->items()->all();

        $this->assertInstanceOf(MenuItemLink::class, $home);
        $this->assertSame(['Home', 'home', 'home', 'fas fa-home'], [$home->title, $home->id, $home->route, $home->icon]);
        $this->assertSame(['filter' => 9], $tasks->parameters);

        $this->assertInstanceOf(MenuItemAction::class, $delete);
        $this->assertSame('wire:click="delete"', $delete->action);
        $this->assertSame('Sure?', $delete->options['confirm']);

        $this->assertSame(MenuItem::class, $system::class);
        $this->assertSame('toggle', $system->options['as']);
    }

    public function test_items_and_dropdown_are_nested_with_siblings(): void
    {
        $menu = (new MenuBuilder)->define([
            MenuItem::make('tasks')->label('Tasks')->route('tasks.index')
                ->items([
                    MenuItem::make('all')->label('All')->route('tasks.index'),
                    MenuItem::make('open')->label('Open')->route('tasks.index', ['filter' => 9]),
                ])
                ->dropdown([
                    MenuItem::make('detail')->label('Detail')->route('projects.show', ['project' => 1]),
                ]),
        ]);

        $tasks = $menu->find('tasks');

        $this->assertSame(['all', 'open'], $tasks->items()->pluck('id')->all());
        $this->assertSame(['detail'], $tasks->dropdown()->pluck('id')->all());
        $this->assertSame($tasks->items(), $tasks->items()->first()->siblings());

        $this->get('/tasks?filter=9');

        $this->assertTrue($tasks->items()->last()->isActive());
        $this->assertFalse($tasks->items()->first()->isActive());
    }

    public function test_invisible_items_and_nulls_are_skipped(): void
    {
        Gate::define('allowed', fn ($user = null) => true);
        Gate::define('denied', fn ($user = null) => false);

        $menu = (new MenuBuilder)->define([
            MenuItem::make('visible')->label('Visible')->visible(fn () => true),
            MenuItem::make('hidden')->label('Hidden')->visible(false)->items([
                MenuItem::make('child')->label('Child'),
            ]),
            false ? MenuItem::make('never') : null,
            MenuItem::make('allowed')->label('Allowed')->can('allowed'),
            MenuItem::make('denied')->label('Denied')->can('denied'),
        ]);

        $this->assertSame(['visible', 'allowed'], $menu->items()->pluck('id')->all());
    }

    public function test_can_uses_the_users_own_can_method(): void
    {
        Gate::define('module', fn ($user = null) => true);

        $this->actingAs(new MenuDefinitionTestUser);

        $menu = (new MenuBuilder)->define([
            MenuItem::make('module')->label('Module')->can('module'),
            MenuItem::make('other')->label('Other')->can('other'),
        ]);

        $this->assertSame(['other'], $menu->items()->pluck('id')->all());
    }

    public function test_label_and_icon_accept_closures_and_items_accept_menu_sets(): void
    {
        $menu = (new MenuBuilder)->define([
            MenuItem::make('tasks')->label(fn () => 'Lazy')->icon(fn () => 'fas fa-list')->items(new MenuDefinitionTestSubMenu),
        ]);

        $tasks = $menu->find('tasks');

        $this->assertSame(['Lazy', 'fas fa-list'], [$tasks->title, $tasks->icon]);
        $this->assertSame(['sub'], $tasks->items()->pluck('id')->all());
    }

    public function test_facade_accepts_menu_set_class_array_and_callback_returning_definitions(): void
    {
        Menu::make('from-class', MenuDefinitionTestSubMenu::class);
        Menu::make('from-array', [MenuItem::make('array')->label('Array')]);
        Menu::make('from-callback', fn (MenuBuilder $menu) => [MenuItem::make('callback')->label('Callback')]);
        Menu::make('imperative', function (MenuBuilder $menu) {
            $menu->addRoute('Home', 'home', 'home');
        });

        $this->assertSame(['sub'], Menu::get('from-class')->items()->pluck('id')->all());
        $this->assertSame(['array'], Menu::get('from-array')->items()->pluck('id')->all());
        $this->assertSame(['callback'], Menu::get('from-callback')->items()->pluck('id')->all());
        $this->assertSame(['home'], Menu::get('imperative')->items()->pluck('id')->all());
    }

    public function test_definitions_go_through_the_builders_item_factory(): void
    {
        $menu = (new MenuDefinitionTestBuilder)->define([
            MenuItem::make('tasks')->label('Tasks')->route('tasks.index')->items([
                MenuItem::make('home')->label('Home')->route('home'),
            ]),
        ]);

        $this->assertInstanceOf(MenuDefinitionTestLink::class, $menu->find('tasks'));
        $this->assertInstanceOf(MenuDefinitionTestLink::class, $menu->find('tasks')->items()->first());
    }
}

/** Uživatel s přepsaným can() - jako User aplikace, který odmítá vypnuté moduly bez ohledu na Gate. */
class MenuDefinitionTestUser extends User
{
    public function can($abilities, $arguments = []): bool
    {
        return $abilities !== 'module';
    }
}

class MenuDefinitionTestSubMenu extends MenuSet
{
    protected function define(): array
    {
        return [
            MenuItem::make('sub')->label('Sub')->route('home'),
        ];
    }
}

class MenuDefinitionTestLink extends MenuItemLink {}

class MenuDefinitionTestBuilder extends MenuBuilder
{
    public static function createMenuItem(string $title, array $options = [])
    {
        if (isset($options['route'])) {
            return new MenuDefinitionTestLink($title, ...$options);
        }

        return parent::createMenuItem($title, $options);
    }
}
