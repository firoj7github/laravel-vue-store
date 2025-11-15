<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
{
    DB::table('products')->insert([
        [
            'name' => 'X Item',
            'quantity' => 100,
            'price' => 6,
            'total_price' => 100 * 6,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'name' => 'Y Item',
            'quantity' => 200,
            'price' => 10,
            'total_price' => 200 * 10,
            'created_at' => now(),
            'updated_at' => now(),
        ],
    ]);
}
}
