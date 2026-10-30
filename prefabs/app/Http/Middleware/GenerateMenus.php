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

        $menuRoutes = [
            'Home' => [
                ' fa-solid fa-home',
                'home',
            ],
        ];

        $systemRoutes = [
            'Settings' => [
                'fa-solid fa-cog',
                'system.setting.index',
            ],
            'Audit' => [
                'fa-solid fa-eye',
                'system.audit.index',
            ],
            'User' => [
                'fa-solid fa-users',
                'system.user.index',
            ],
            'Subscriptions' => [
                'fa-solid fa-dollar-sign',
                'system.subscription.index',
            ],
            'File' => [
                'fa-solid fa-folder',
                'system.file.index',
            ],
            'Logs' => [
                'fa-solid fa-bug',
                'system.log.index',
            ],
            'Jobs' => [
                'fa-solid fa-business-time',
                'system.jobs.index',
            ],
            'Cache' => [
                'fa-solid fa-box',
                'system.cache.index',
            ],
            'Backup' => [
                'fa-solid fa-file-archive',
                'system.backup.index',
            ],
        ];

        if (file_exists(base_path() . '/routes/api.php')) {
            $systemRoutes['Api'] = [
                'fa-solid fa-rocket',
                'system.api.index',
            ];
        }

        $menus = [
            'main-menu'   => $menuRoutes,
            'system-menu' => $systemRoutes,
        ];

        foreach ($menus as $menuKey => $MenuItems) {
            $menu = Menu::get($menuKey) ?? Menu::make($menuKey, function () {});
            foreach ($MenuItems as $title => $route_data) {
                $icon = $route_data[0];
                $route = $route_data[1];

                $menu->add($title, [
                    'id'    => strtolower($title),
                    'icon'  => $icon,
                    'route' => $route,
                ]);
            }
        }

        return $next($request);
    }
}
