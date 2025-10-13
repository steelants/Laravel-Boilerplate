<?php

namespace SteelAnts\LaravelBoilerplate\Listeners;

use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Events\Dispatcher;
use SteelAnts\LaravelBoilerplate\Services\ActivityService;

class UserEventSubscriber
{
    public function handleLogin(Login $event): void
	{
		ActivityService::log(__('Login'));
	}

    public function handleLogout(Logout $event): void
	{
		ActivityService::log(__('Logout'));
	}

    public function handleFailed(Failed $event): void
	{
		ActivityService::log(__('Failed login'), ['email' => $event->credentials['email']]);
	}

    public function subscribe(Dispatcher $events): array
    {
        return [
            Login::class  => 'handleLogin',
            Logout::class => 'handleLogout',
            Failed::class => 'handleFailed',
        ];
    }
}
