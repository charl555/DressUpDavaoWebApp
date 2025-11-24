<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KiriEngineJobs extends Model
{
    protected $table = 'kiri_engine_jobs';
    protected $primaryKey = 'kiri_engine_job_id';

    protected $fillable = [
        'user_id',
        'product_id',
        'serialize_id',
        'status',
        'model_url',
        'kiri_options',
        'is_downloaded',
        'url_expiry',
        'notes',
        'error_message',
    ];

    protected $casts = [
        'kiri_options' => 'array',
        'is_downloaded' => 'boolean',
        'url_expiry' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function stored3dModel()
    {
        return $this->hasOne(Stored3dModels::class, 'kiri_engine_job_id', 'kiri_engine_job_id');
    }

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id', 'product_id');
    }
}
