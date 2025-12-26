<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Kalnoy\Nestedset\NodeTrait;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Taxonomy extends Model
{
    use NodeTrait, HasSlug;

    protected $fillable = ['name', 'slug', 'type', 'parent_id'];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function posts() : BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_taxonomy');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}