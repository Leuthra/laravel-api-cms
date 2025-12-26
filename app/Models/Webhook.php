<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Webhook extends Model
{
    use LogsActivity;

    protected $fillable = [
        'name', 
        'url', 
        'event', 
        'secret', 
        'is_active', 
        'headers'
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'headers' => 'array',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'url', 'event', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}