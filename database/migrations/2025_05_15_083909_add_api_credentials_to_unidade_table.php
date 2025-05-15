<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApiCredentialsToUnidadeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('unidade', function (Blueprint $table) {
            $table->text('uni_api_login')->nullable()->after('uni_api');
            $table->text('uni_api_password')->nullable()->after('uni_api_login');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('unidade', function (Blueprint $table) {
            $table->dropColumn('uni_api_login');
            $table->dropColumn('uni_api_password');
        });
    }
}