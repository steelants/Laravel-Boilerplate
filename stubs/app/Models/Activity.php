<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Observers\ActivityObserver;

class Activity extends Model
{
    use HasFactory;

    protected $casts = [
        'data' => 'array',
    ];
    const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ip',
        'user_id',
        'performed_id',
        'lang_text',
        'data',
    ];

     /**
    * The "booted" method of the model.
    *
    * @return void
    */
    protected static function booted()
    {
        Activity::observe(ActivityObserver::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function performed()
    {
        return $this->belongsTo(User::class, 'performed_id');
    }
}