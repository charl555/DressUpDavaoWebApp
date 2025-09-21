<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMeasurements extends Model
{
    protected $table = 'user_measurements';
    protected $primaryKey = 'user_measurements_id';

    protected $fillable = [
        'user_id',
        'chest',
        'waist',
        'hips',
        'shoulder',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
