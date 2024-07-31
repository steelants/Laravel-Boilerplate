<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        if (Auth::guest()) {
            return $next($request);
        }
        
        if ($request->route()->getName() === 'livewire.message') {
        	return $next($request);
        }
        
        Menu::make('main-menu', function ($menu) {
            $systemRoutes = [
                'boilerplate::ui.home' => [' fas fa-home', 'home'],
            ];
            foreach ($systemRoutes as $title => $route_data) {
                $icon = $route_data[0];
                $route = $route_data[1];

                $menu = $menu->add($title, [
                    'id' => strtolower($title),
                    'icon' => $icon,
                    'route' => $route,
                ]);
            }
        });

        //CHECK IF USER IS SYSTEM ADMIN
        Menu::make('system-menu', function ($menu) {
            $systemRoutes = [
                'boilerplate::ui.audit' => ['fas fa-eye', 'system.audit.index'],
                'boilerplate::ui.api' => ['fas fa-file-archive', 'system.api.index'],
                'boilerplate::ui.user' => ['fas fa-users', 'system.user.index'],
                'boilerplate::subscriptions.title' => ['fas fa-dollar-sign', 'system.subscription.index'],
                'boilerplate::ui.log' => ['fas fa-bug', 'system.log.index'],
                'boilerplate::ui.jobs' => ['fas fa-business-time', 'system.jobs.index'],
                'boilerplate::ui.cache' => ['fas fa-box', 'system.cache.index'],
                'boilerplate::ui.backup' => ['fas fa-file-archive', 'system.backup.index']
            ];
            foreach ($systemRoutes as $title => $route_data) {
                $icon = $route_data[0];
                $route = $route_data[1];

                $menu = $menu->add($title, [
                    'id' => strtolower($title),
                    'icon' => $icon,
                    'route' => $route,
                ]);
            }
        });

        return $next($request);
    }
}
