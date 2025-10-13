<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use SteelAnts\LaravelBoilerplate\Observers\SettingObserver;

#[ObservedBy([SettingObserver::class])]
class Setting extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'index',
        'value',
        'type',
    ];

    public function settingable(): MorphTo
    {
        return $this->morphTo();
    }
}
