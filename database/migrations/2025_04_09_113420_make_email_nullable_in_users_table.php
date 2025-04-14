<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Primeiro, faça uma consulta SQL direta para remover qualquer restrição UNIQUE na coluna
        DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_use_email_unique');
        
        // Depois, altera a coluna para ser nullable
        Schema::table('users', function (Blueprint $table) {
            $table->string('use_email')->nullable()->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('use_email')->nullable(false)->unique()->change();
        });
    }
};