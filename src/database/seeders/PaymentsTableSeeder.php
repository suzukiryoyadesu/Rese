<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $payment = [
            'name' => '現地決済'
        ];
        DB::table('payments')->insert($payment);
        $payment = [
            'name' => 'Stripe決済'
        ];
        DB::table('payments')->insert($payment);
    }
}
