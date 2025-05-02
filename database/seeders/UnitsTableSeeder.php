<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UnitsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('units')->delete();
        
        \DB::table('units')->insert(array (
            0 => 
            array (
                'unit_id' => 142,
                'unit_uni_id' => 31,
                'unit_use_id' => 63,
                'created_at' => '2025-04-29 17:10:21',
                'updated_at' => '2025-04-29 17:10:21',
            ),
            1 => 
            array (
                'unit_id' => 144,
                'unit_uni_id' => 31,
                'unit_use_id' => 66,
                'created_at' => '2025-04-29 17:37:07',
                'updated_at' => '2025-04-29 17:37:07',
            ),
            2 => 
            array (
                'unit_id' => 155,
                'unit_uni_id' => 31,
                'unit_use_id' => 61,
                'created_at' => '2025-04-30 14:13:34',
                'updated_at' => '2025-04-30 14:13:34',
            ),
            3 => 
            array (
                'unit_id' => 156,
                'unit_uni_id' => 31,
                'unit_use_id' => 67,
                'created_at' => '2025-04-30 14:15:11',
                'updated_at' => '2025-04-30 14:15:11',
            ),
            4 => 
            array (
                'unit_id' => 157,
                'unit_uni_id' => 31,
                'unit_use_id' => 43,
                'created_at' => '2025-04-30 15:53:25',
                'updated_at' => '2025-04-30 15:53:25',
            ),
            5 => 
            array (
                'unit_id' => 158,
                'unit_uni_id' => 31,
                'unit_use_id' => 68,
                'created_at' => '2025-04-30 15:55:33',
                'updated_at' => '2025-04-30 15:55:33',
            ),
        ));
        
        
    }
}