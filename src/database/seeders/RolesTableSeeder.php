<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $role = [
            'name' => '利用者',
        ];
        DB::table('roles')->insert($role);
        $role = [
            'name' => '店舗代表者',
        ];
        DB::table('roles')->insert($role);
        $role = [
            'name' => '管理者',
        ];
        DB::table('roles')->insert($role);
    }
}
