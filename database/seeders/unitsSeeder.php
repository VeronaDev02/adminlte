<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitsSeeder extends Seeder
{
    public function run()
    {
        DB::table('units')->insert([
            'unit_id' => 131,
            'unit_uni_id' => 13,
            'unit_use_id' => 1,
            'created_at' => '2025-04-09 17:14:25',
            'updated_at' => '2025-04-09 17:14:25',
        ]);
        DB::table('units')->insert([
            'unit_id' => 134,
            'unit_uni_id' => 1,
            'unit_use_id' => 43,
            'created_at' => '2025-04-09 17:15:10',
            'updated_at' => '2025-04-09 17:15:10',
        ]);
        DB::table('units')->insert([
            'unit_id' => 85,
            'unit_uni_id' => 1,
            'unit_use_id' => 1,
            'created_at' => '2025-04-09 13:08:21',
            'updated_at' => '2025-04-09 13:08:21',
        ]);
    }
}
