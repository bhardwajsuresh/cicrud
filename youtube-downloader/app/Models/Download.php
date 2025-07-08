<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Download extends Model
{
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';

    protected $fillable = [
        'url',
        'video_id',
        'title',
        'duration',
        'status',
        'file_path',
        'file_size',
        'format',
        'error_message',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array'
    ];

    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isFailed()
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function isProcessing()
    {
        return $this->status === self::STATUS_PROCESSING;
    }
}
