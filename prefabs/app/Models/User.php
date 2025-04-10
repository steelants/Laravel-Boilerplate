<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Observers\UserObserver;
use SteelAnts\LaravelBoilerplate\Traits\HasSettings;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\HasApiTokens;
use SteelAnts\LaravelBoilerplate\Traits\Auditable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([UserObserver::class])]
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, Auditable, HasSettings;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    protected function isSystemAdmin(): Attribute
    {
        return Attribute::make(
            get: fn() => in_array($this->id, config('boilerplate.system_admins')),
        )->shouldCache();
    }

    protected function limitationSetting(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->settings()->where("index", "limitation.items_per_page")->first()->value ?? null,
        )->shouldCache();
    }

    private static function parseSettingsToArray($settingsRaw)
    {
        $settings = [];
        if (!empty($settingsRaw->value)) {
            foreach (json_decode($settingsRaw->value, true) as $driver => $types) {
                foreach ($types as $type) {
                    $settings[$driver][$type] = true;
                }
            }
        }

        return $settings;
    }

    public function getSortPreference(): string
    {
        return once(fn(): string => Cache::remember(sprintf('user-%d-sorting-preference', $this->id), 500, function () {
            return (!empty($this->settings()->where('index', 'profile.sort')->first()->value) ? 'desc' : 'asc');
        }));
    }

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }
}
