<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MigrationsSeeder extends Seeder
{
    public function run()
    {
        DB::table('migrations')->insert([
            'id' => 1,
            'migration' => '2025_04_08_014822_create_unidade_table',
            'batch' => 1,
        ]);
        DB::table('migrations')->insert([
            'id' => 2,
            'migration' => '2025_04_08_014847_create_role_table',
            'batch' => 1,
        ]);
        DB::table('migrations')->insert([
            'id' => 3,
            'migration' => '2025_04_08_014901_create_users_table',
            'batch' => 1,
        ]);
        DB::table('migrations')->insert([
            'id' => 4,
            'migration' => '2025_04_08_014915_create_selfs_table',
            'batch' => 1,
        ]);
        DB::table('migrations')->insert([
            'id' => 5,
            'migration' => '2025_04_08_014936_create_units_table',
            'batch' => 1,
        ]);
        DB::table('migrations')->insert([
            'id' => 6,
            'migration' => '2025_04_08_121350_remove_use_uni_id_from_users_table',
            'batch' => 2,
        ]);
    }
}
