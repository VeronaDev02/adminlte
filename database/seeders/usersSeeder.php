<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert([
            'use_id' => 1,
            'use_name' => 'Lucas Gabriel Costa',
            'use_email' => 'suporte6@veronasupermercados.com.br',
            'use_cod_func' => 001,
            'use_last_seen' => '',
            'use_ip_origin' => '',
            'use_username' => 'lucas_costa',
            'use_password' => '$2y$10$ltncChMbbRfYFdm1RaiOZe4.OK3gP1uGyE4184Zh7pgrYvS0v9uiW',
            'use_cell' => '(11) 99999-9999',
            'use_active' => '1',
            'use_login_ativo' => '1',
            'use_allow_updates' => '1',
            'use_rol_id' => 1,
            'remember_token' => '',
            'created_at' => '2025-04-08 11:02:24',
            'updated_at' => '2025-04-09 15:48:23',
        ]);
        DB::table('users')->insert([
            'use_id' => 43,
            'use_name' => 'Kurosaki Ichigo',
            'use_email' => 'hollow@shinigami.com.br',
            'use_cod_func' => 2222222222,
            'use_last_seen' => '',
            'use_ip_origin' => '',
            'use_username' => 'kurosaki_ichigo',
            'use_password' => '$2y$10$DM8FuLJUXOPn/w6VNH3Lk.NhmXETFFhI78oqv4YB5x4/Dc6wVD2D6',
            'use_cell' => '(11) 11111-1111',
            'use_active' => '',
            'use_login_ativo' => '1',
            'use_allow_updates' => '1',
            'use_rol_id' => 1,
            'remember_token' => '',
            'created_at' => '2025-04-09 17:15:10',
            'updated_at' => '2025-04-09 18:37:46',
        ]);
    }
}
