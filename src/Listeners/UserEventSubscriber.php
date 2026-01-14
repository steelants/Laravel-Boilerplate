<?php

namespace SteelAnts\LaravelBoilerplate\Listeners;

use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Events\Dispatcher;
use SteelAnts\LaravelBoilerplate\Services\ActivityService;

class UserEventSubscriber
{
	public function __construct(private ActivityService $activityService)
	{
	}

    public function handleLogin(Login $event): void
	{
		$this->activityService->log(__('Login'));
	}

    public function handleLogout(Logout $event): void
	{
		$this->activityService->log(__('Logout'));
	}

    public function handleFailed(Failed $event): void
	{
		$this->activityService->log(__('Failed login'), ['email' => $event->credentials['email']]);
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
