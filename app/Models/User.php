<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'organization_id',
        'is_admin',
        'is_org_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_org_admin' => 'boolean',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // Admin panel is only for platform admins
        if ($panel->getId() === 'admin') {
            return $this->is_admin;
        }

        return true;
    }

    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    public function isOrgAdmin(): bool
    {
        return $this->is_org_admin || $this->is_admin;
    }

    public function canManageOrganization(Organization $organization): bool
    {
        if ($this->is_admin) {
            return true;
        }

        return $this->organization_id === $organization->id && $this->is_org_admin;
    }
}
