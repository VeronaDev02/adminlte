<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run()
    {
        DB::table('role')->insert([
            'rol_id' => 9,
            'rol_name' => 'TI',
            'created_at' => '2025-04-09 17:17:33',
            'updated_at' => '2025-04-09 17:17:33',
        ]);
        DB::table('role')->insert([
            'rol_id' => 1,
            'rol_name' => 'Administrador',
            'created_at' => '2025-04-08 11:02:23',
            'updated_at' => '2025-04-09 16:00:46',
        ]);
    }
}
