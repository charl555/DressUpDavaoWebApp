<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stored3dModels extends Model
{
    use HasFactory;

    protected $table = 'stored3d_models';
    protected $primaryKey = 'stored_3d_model_id';

    protected $fillable = [
        'user_id',
        'kiri_engine_job_id',
        'model_name',
        'model_path',
        'original_filename',
        'file_size',
        'model_files',
    ];

    protected $casts = [
        'model_files' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function kiriEngineJob()
    {
        return $this->belongsTo(KiriEngineJobs::class, 'kiri_engine_job_id', 'kiri_engine_job_id');
    }
}
