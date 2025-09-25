<?php

namespace SteelAnts\LaravelBoilerplate\Models;

use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Model;

class FailedJob extends Model
{
	protected $casts = [
        'payload'   => AsCollection::class,
		'failed_at' => 'datetime',
    ];
}
