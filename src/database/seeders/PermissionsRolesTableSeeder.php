<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class PermissionsRolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::find(2);
        $permissions = array('restaurant', 'reservation');
        $role->givePermissions($permissions);
        $role = Role::find(3);
        $permissions = array('restaurant', 'reservation', 'admin');
        $role->givePermissions($permissions);
    }
}
