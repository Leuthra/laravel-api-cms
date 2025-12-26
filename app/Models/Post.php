<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Post extends Model implements HasMedia
{
    use SoftDeletes, HasSlug, LogsActivity, InteractsWithMedia;

    protected $fillable = [
        'user_id', 'title', 'slug', 'content', 'type', 
        'status', 'payload', 'seo', 'published_at'
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'seo' => 'array',
            'published_at' => 'datetime',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'status', 'payload', 'seo'])
            ->logOnlyDirty() 
            ->dontSubmitEmptyLogs();
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function taxonomies()
    {
        return $this->belongsToMany(Taxonomy::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}