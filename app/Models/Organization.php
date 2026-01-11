<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Organization extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'max_pages',
        'min_crawl_interval_hours',
        'max_filmstrip_retention',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'max_pages' => 'integer',
        'min_crawl_interval_hours' => 'integer',
        'max_filmstrip_retention' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($organization) {
            if (empty($organization->slug)) {
                $organization->slug = Str::slug($organization->name);
            }
        });
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function pages(): HasMany
    {
        return $this->hasMany(Page::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function activePages(): HasMany
    {
        return $this->pages()->where('is_active', true);
    }

    public function canAddMorePages(): bool
    {
        return $this->pages()->count() < $this->max_pages;
    }
}
