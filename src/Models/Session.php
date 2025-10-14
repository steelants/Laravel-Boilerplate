<?php

namespace SteelAnts\LaravelBoilerplate\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;
    protected $casts = ['last_activity' => 'datetime'];
    protected $appends = [
        'browser_name',
        'browser_os_name',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected function browserName(): Attribute
    {
        return Attribute::make(
            get: function () {
                $pattern = '/\b(Edge|Safari|Chrome|Firefox|Opera)\b/i'; // Regular expression pattern to match common browser names
                $matches = [];
                preg_match($pattern, $this->user_agent, $matches);

                if (isset($matches[1])) {
                    return $matches[1];
                }

                return __('Unknown');
            },
        );
    }

    protected function browserOSName(): Attribute
    {
        return Attribute::make(
            get: function () {
                $pattern = '/\b(Android|Linux|Windows|iOS|MacOS)\b/i'; // Regular expression pattern to match common browser names
                $matches = [];
                preg_match($pattern, $this->user_agent, $matches);

                if (isset($matches[1])) {
                    return $matches[1];
                }

                return __('Unknown');
            },
        );
    }
}
