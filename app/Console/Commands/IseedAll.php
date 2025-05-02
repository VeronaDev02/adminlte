<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class IseedAll extends Command
{
    protected $signature = 'iseed:all';
    protected $description = 'Gera seeders para todas as tabelas do banco, exceto migrations';

    public function handle()
    {
        $database = config('database.default');
        $schema = DB::connection()->getDoctrineSchemaManager();
        $tables = $schema->listTableNames();

        foreach ($tables as $table) {
            if ($table === 'migrations') continue;

            $this->info("Gerando seeder para tabela: {$table}");
            Artisan::call('iseed', ['tables' => $table]);
            $this->info(Artisan::output());
        }

        $this->info("Seeders gerados com sucesso!");
    }
}
