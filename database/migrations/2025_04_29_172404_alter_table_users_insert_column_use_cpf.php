<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableUsersInsertColumnUseCpf extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('use_cpf')->unique()->after('use_name');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('use_cpf');
        });
    }
}
