<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\ChangePermissions;

class Role extends Model
{
    use HasFactory;
    use ChangePermissions;

    protected $fillable = [
        'name',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'roles_permissions', 'role_id', 'permission_id');
    }

    public function hasPermission(String $permission)
    {
        return (bool) $this->permissions->where('name', $permission)->count();
    }
}
