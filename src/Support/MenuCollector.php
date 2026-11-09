<?php

namespace SteelAnts\LaravelBoilerplate\Support;

use Illuminate\Support\Collection;

class MenuCollector
{
    protected Collection $menus;

    public function __construct()
    {
        $this->menus = new Collection;
    }

    /**
     * Vytvoří (a pod klíčem zaregistruje) menu. Definice může být:
     *  - MenuSet nebo název jeho třídy (resolvuje se z containeru),
     *  - pole definic MenuItem::make(...),
     *  - callback, který dostane MenuBuilder - může ho plnit imperativně, nebo vrátit pole definic.
     *
     * @param  MenuSet|class-string<MenuSet>|array|callable|null  $definition
     */
    public function make(string $menuKey = '', MenuSet|string|array|callable|null $definition = null)
    {
        $menu = app(MenuBuilder::class);

        if (is_string($definition) && is_subclass_of($definition, MenuSet::class)) {
            $definition = app($definition);
        }

        if ($definition instanceof MenuSet || (is_array($definition) && !is_callable($definition))) {
            $menu->define($definition);
        } elseif (is_callable($definition)) {
            $result = call_user_func($definition, $menu);

            if (is_array($result) || $result instanceof MenuSet) {
                $menu->define($result);
            }
        }

        if (!empty($menuKey)) {
            $this->menus->put($menuKey, $menu);
        }

        return $menu;
    }

    public function get(string $menuKey)
    {
        return $this->menus->get($menuKey);
    }
}
