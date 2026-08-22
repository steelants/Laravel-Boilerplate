<?php

namespace SteelAnts\LaravelBoilerplate\Models;

use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    /**
     * The queue driver writes the rows itself - created_at is an unsigned int (unix
     * timestamp) and there is no updated_at column at all, so Eloquent must not
     * maintain either of them. Reading them through $casts still works.
     */
    public $timestamps = false;

    protected $casts = [
        'payload'      => AsCollection::class,
        'reserved_at'  => 'datetime',
        'available_at' => 'datetime',
        'created_at'   => 'datetime',
    ];

    /**
     * A worker stamps reserved_at when it picks the job up, so a job that still
     * has it null is only sitting in the queue.
     */
    public function isReserved(): bool
    {
        return $this->reserved_at !== null;
    }

    /**
     * Job is scheduled for later - available_at has not come yet.
     */
    public function isDelayed(): bool
    {
        return !$this->isReserved() && $this->available_at !== null && $this->available_at->isFuture();
    }
}
