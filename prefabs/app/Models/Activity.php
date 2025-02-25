<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SteelAnts\LaravelBoilerplate\Observers\ActivityObserver as ObserversActivityObserver;

class Activity extends Model
{
    use HasFactory;

    protected $casts = ['data' => 'array'];
    const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ip',
        'actor_type',
        'actor_id',
        'affected_type',
        'affected_id',
        'lang_text',
        'data',
    ];

    protected static function booted()
    {
        Activity::observe(ObserversActivityObserver::class);
    }

    public function actor()
    {
        return $this->morphTo('actor');
    }

    public function affected()
    {
        return $this->morphTo('affected');
    }
}
