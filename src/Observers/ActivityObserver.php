<?php

namespace SteelAnts\LaravelBoilerplate\Observers;

use App\Models\Activity;

class ActivityObserver
{
    /**
     * Handle the Activity "created" event.
     */
    public function creating(Activity $activity): void
    {
        if (!app()->runningInConsole()) {
            $activity->ip = $this->getIp();
            if (empty($activity->actor_id) && auth()->check()) {
                $activity->actor()->associate(auth()->user());
            }
        } else {
            $activity->ip = "localhost";
        }
    }

    public function getIp()
    {
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip); // just to be safe
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        return request()->ip(); // it will return server ip when no client ip found
    }
}
