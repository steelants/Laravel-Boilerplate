<?php

namespace SteelAnts\LaravelBoilerplate\Models;

use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
	protected $casts = [
        'payload'      => AsCollection::class,
		'available_at' => 'datetime',
    ];
}
