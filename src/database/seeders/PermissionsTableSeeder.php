<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permission = [
            'name' => 'representative',
        ];
        DB::table('permissions')->insert($permission);
        $permission = [
            'name' => 'restaurant',
        ];
        DB::table('permissions')->insert($permission);
        $permission = [
            'name' => 'reservation',
        ];
        DB::table('permissions')->insert($permission);
    }
}
