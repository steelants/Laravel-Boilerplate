<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class TenantUser extends Pivot
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'organization_id',
        'permission',
    ];

    public function user() {
        return $this->belongsTo('User');
    }

    public function tenant() {
        return $this->belongsTo('Tenant');
    }
}