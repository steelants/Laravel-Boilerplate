<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use SteelAnts\LaravelBoilerplate\Facades\Menu;
use Symfony\Component\HttpFoundation\Response;

class GenerateMenus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Menu::make('menu', function ($menu) {
            $systemRoutes = [];
            foreach ($systemRoutes as $title => $route) {
                $menu = $menu->add( $title, [
                    'id' => strtolower($title),
                    'route' => $route
                ]);
            }
        });

        //CHECK IF USER IS SYSTEM ADMIN
        Menu::make('system-menu', function ($menu) {
            $systemRoutes = [
                'boilerplate::ui.audit' => 'system.audit.index',
                'boilerplate::ui.user' => 'system.user.index',
                'boilerplate::subscriptions.title' => 'system.subscription.index',
                'boilerplate::ui.log' => 'system.log.index',
                'boilerplate::ui.jobs' => 'system.jobs.index',
                'boilerplate::ui.cache' => 'system.cache.index',
                'boilerplate::ui.backup' => 'system.backup.index'
            ];
            foreach ($systemRoutes as $title => $route) {
                $menu = $menu->add( $title, [
                    'id' => strtolower($title),
                    'route' => $route
                ]);
            }
        });

        return $next($request);
    }
}
