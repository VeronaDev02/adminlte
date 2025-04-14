<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;  // Importando a classe Str

class GenerateSeeders extends Command
{
    protected $signature = 'generate:seeders';
    protected $description = 'Gera seeders com os dados das tabelas do banco de dados';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Verifica o tipo de banco de dados
        if (config('database.default') === 'pgsql') {
            // Para PostgreSQL
            $tables = DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'");
        } else {
            // Para MySQL/MariaDB
            $tables = DB::select('SHOW TABLES');
        }

        foreach ($tables as $table) {
            $tableName = $table->table_name ?? $table->Tables_in_nome_do_banco;  // Ajuste conforme necessário
            $rows = DB::table($tableName)->get();

            // Iniciando o conteúdo do Seeder
            $seederContent = "<?php\n\n";
            $seederContent .= "use Illuminate\Database\Seeder;\n";
            $seederContent .= "use Illuminate\Support\Facades\DB;\n\n";
            $seederContent .= "class " . ucfirst(Str::camel($tableName)) . "Seeder extends Seeder\n";  // Usando Str::camel
            $seederContent .= "{\n";
            $seederContent .= "    public function run()\n";
            $seederContent .= "    {\n";

            // Gerando os inserts dos dados das tabelas
            foreach ($rows as $row) {
                $seederContent .= "        DB::table('{$tableName}')->insert([\n";
                foreach ($row as $column => $value) {
                    $seederContent .= "            '{$column}' => " . (is_numeric($value) ? $value : "'{$value}'") . ",\n";
                }
                $seederContent .= "        ]);\n";
            }

            $seederContent .= "    }\n";
            $seederContent .= "}\n";

            // Salvando o arquivo do Seeder na pasta database/seeders
            File::put(database_path("seeders/{$tableName}Seeder.php"), $seederContent);
        }

        $this->info('Seeders gerados com sucesso!');
    }
}
