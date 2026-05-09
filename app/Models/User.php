<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username', 'email', 'password', 'phone_number',
        'account_status', 'is_claimed', 'role', 'is_kol'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_claimed' => 'boolean',
            'is_kol' => 'boolean',
        ];
    }

    public function hasRole(string|array $roles): bool
    {
        if (is_string($roles)) {
            $roles = [$roles];
        }
        foreach ($roles as $role) {
            if ($this->role === strtoupper($role)) {
                return true;
            }
        }
        return false;
    }

    public function importHistories() { return $this->hasMany(ImportHistory::class, 'admin_id'); }
    public function creatorMetrics() { return $this->hasMany(CreatorMetric::class); }
    public function videos() { return $this->hasMany(Video::class); }
    public function blacklists() { return $this->hasMany(Blacklist::class); }
    public function sampleRequests() { return $this->hasMany(SampleRequest::class); }
    public function liveStreams() { return $this->hasMany(LiveStream::class); }
    public function kolContracts() { return $this->hasMany(KOLContract::class); }
    public function activeContract() { return $this->hasOne(KOLContract::class)->where('status', 'ACTIVE')->latest(); }
}