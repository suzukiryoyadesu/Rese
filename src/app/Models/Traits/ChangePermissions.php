<?php

namespace App\Models\Traits;

use App\Models\Permission;

trait ChangePermissions
{
    public function givePermissions(array $permissions)
    {
        $permissions = $this->getPermissions($permissions);
        if ($permissions->isEmpty()) return $this;

        foreach ($permissions as $perm) {
            if (!$this->hasPermission($perm->name)) {
                $this->permissions()->attach($perm->id);
            }
        }
        return $this;
    }

    public function removePermissions(array $permissions)
    {
        $permissions = $this->getPermissions($permissions);
        if ($permissions->isEmpty()) return $this;

        foreach ($permissions as $perm) {
            if ($this->hasPermission($perm->name)) {
                $this->permissions()->detach($perm->id);
            }
        }
        return $this;
    }

    public function refreshPermissions(array $permissions)
    {
        $this->permissions()->detach();
        return $this->givePermissions($permissions);
    }

    protected function getPermissions(array $permissions)
    {
        return Permission::whereIn('name', $permissions)->get();
    }
}
