<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoriteToRestaurants()
    {
        return $this->belongsToMany(Restaurant::class, 'favorites', 'user_id', 'restaurant_id');
    }

    public function isFavorite($restaurant_id)
    {
        return $this->favorites()->where('restaurant_id', $restaurant_id)->exists();
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function hasRole(String|int $role)
    {
        return ($this->role->name == $role) || ($this->role->id == $role);
    }

    public function hasPermission(String $permission)
    {
        return (bool) $this->role->permissions->where('name', $permission)->count();
    }

    public function toRestaurants()
    {
        return $this->belongsToMany(Restaurant::class, 'restaurants_users', 'user_id', 'restaurant_id');
    }
}
