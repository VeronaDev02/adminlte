<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('use_id');
            $table->string('use_name');
            $table->string('use_email')->nullable()->unique();
            $table->string('use_cod_func')->unique();
            $table->timestamp('use_last_seen')->nullable();
            $table->string('use_ip_origin')->nullable();
            $table->string('use_username')->unique();
            $table->string('use_password');
            $table->string('use_cell')->nullable();
            $table->boolean('use_active')->default(true);
            $table->boolean('use_login_ativo')->default(true);
            $table->bigInteger('use_rol_id');
            $table->timestamps();
            $table->text('img_user')->nullable();
            $table->boolean('use_status_password')->default(false);
            $table->json('ui_preferences')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
