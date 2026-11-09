<?php

namespace SteelAnts\LaravelBoilerplate\Support;

/**
 * Definice menu (nebo jeho části) jako třída - obdoba ActionSet. Lze ji předat do Menu::make('key', AppMenu::class),
 * MenuBuilder::define() nebo jako podpoložky ->items(new TaskMenu):
 *
 *     class AppMenu extends MenuSet
 *     {
 *         protected function define(): array
 *         {
 *             return [
 *                 MenuItem::make('home')->label(__('Home'))->icon('fas fa-home')->route('home'),
 *                 MenuItem::make('tasks')->label(__('Tasks'))->route('task.index')->can('tasks')->items(new TaskMenu),
 *             ];
 *         }
 *     }
 */
abstract class MenuSet
{
    /** @return list<PendingMenuItem|null> */
    abstract protected function define(): array;

    /** @return list<PendingMenuItem> */
    public function items(): array
    {
        return PendingMenuItem::resolve($this->define());
    }
}
