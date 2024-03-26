<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'domain',
    ];

    public function setDomainAttribute($value)
    {
        $this->attributes['domain'] = strtolower($value);
    }
}
