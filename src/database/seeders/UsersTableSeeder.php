<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = [
            'name' => '鈴木',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role_id' => '3',
        ];
        DB::table('users')->insert($user);
        $user = [
            'name' => '山田',
            'email' => 'test1@example.com',
            'password' => bcrypt('password'),
            'role_id' => '1',
        ];
        DB::table('users')->insert($user);
        $user = [
            'name' => '田中',
            'email' => 'test2@example.com',
            'password' => bcrypt('password'),
            'role_id' => '1',
        ];
        DB::table('users')->insert($user);
    }
}
