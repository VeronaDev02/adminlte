<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            
        ]);
        $this->call(TipoUnidadeTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(RoleTableSeeder::class);
        $this->call(UnitsTableSeeder::class);
        $this->call(UnidadeTableSeeder::class);
        $this->call(SelfsTableSeeder::class);
        $this->call(ContextLogMessagesUserTableSeeder::class);
        $this->call(LogMessagesUserTableSeeder::class);
    }
}