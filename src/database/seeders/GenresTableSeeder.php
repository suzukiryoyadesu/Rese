<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GenresTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $genre = [
            'name' => '寿司'
        ];
        DB::table('genres')->insert($genre);
        $genre = [
            'name' => '焼肉'
        ];
        DB::table('genres')->insert($genre);
        $genre = [
            'name' => '居酒屋'
        ];
        DB::table('genres')->insert($genre);
        $genre = [
            'name' => 'イタリアン'
        ];
        DB::table('genres')->insert($genre);
        $genre = [
            'name' => 'ラーメン'
        ];
        DB::table('genres')->insert($genre);
    }
}
