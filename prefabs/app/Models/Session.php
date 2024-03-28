<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    use HasFactory;
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;
    protected $casts = [
        'last_activity' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    function getBrowserNameAttribute()
    {
        $pattern = '/\b(Safari|Chrome|Firefox|Opera)\b/i'; // Regular expression pattern to match common browser names
        $matches = [];
        preg_match($pattern, $this->user_agent, $matches);

        if (isset($matches[1])) {
            $browserName = $matches[1];
            return $browserName;
        }

        return 'Unknown';
    }

    function getBrowserOSNameAttribute()
    {
        $pattern = '/\b(Android|Linux|Windows|iOS|MacOS)\b/i'; // Regular expression pattern to match common browser names
        $matches = [];
        preg_match($pattern, $this->user_agent, $matches);

        if (isset($matches[1])) {
            $browserName = $matches[1];
            return $browserName;
        }

        return 'Unknown';
    }
}
