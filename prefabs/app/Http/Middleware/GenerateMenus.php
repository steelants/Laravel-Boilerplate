<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SteelAnts\LaravelBoilerplate\Facades\Menu;
use SteelAnts\LaravelBoilerplate\Support\MenuBuilder;
use SteelAnts\LaravelBoilerplate\Support\MenuItem;
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

        $this->menu('main-menu')->define([
            MenuItem::make('home')->label('Home')->icon('fas fa-home')->route('home'),
        ]);

        $this->menu('system-menu')->define([
            MenuItem::make('settings')->label('Settings')->icon('fas fa-cog')->route('system.setting.index'),
            MenuItem::make('audit')->label('Audit')->icon('fas fa-eye')->route('system.audit.index'),
            MenuItem::make('user')->label('User')->icon('fas fa-users')->route('system.user.index'),
            MenuItem::make('subscriptions')->label('Subscriptions')->icon('fas fa-dollar-sign')->route('system.subscription.index'),
            MenuItem::make('file')->label('File')->icon('fas fa-folder')->route('system.file.index'),
            MenuItem::make('logs')->label('Logs')->icon('fas fa-bug')->route('system.log.index'),
            MenuItem::make('jobs')->label('Jobs')->icon('fas fa-business-time')->route('system.jobs.index'),
            MenuItem::make('cache')->label('Cache')->icon('fas fa-box')->route('system.cache.index'),
            MenuItem::make('backup')->label('Backup')->icon('fas fa-file-archive')->route('system.backup.index'),
            MenuItem::make('api')->label('Api')->icon('fas fa-rocket')->route('system.api.index')
                ->visible(fn () => file_exists(base_path() . '/routes/api.php')),
        ]);

        return $next($request);
    }

    /** Menu pod daným klíčem - existující (doplněné jinde) nebo nové. */
    private function menu(string $key): MenuBuilder
    {
        return Menu::get($key) ?? Menu::make($key);
    }
}
