<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('use_id');
            $table->string('use_name');
            $table->string('use_email')->unique();
            $table->string('use_cod_func')->nullable();
            $table->timestamp('use_last_seen')->nullable();
            $table->string('use_ip_origin')->nullable();
            $table->string('use_username')->unique();
            $table->string('use_password');
            $table->string('use_cell')->nullable();
            $table->boolean('use_active')->default(true);
            $table->boolean('use_login_ativo')->default(true);
            $table->boolean('use_allow_updates')->default(false);
            $table->unsignedBigInteger('use_uni_id')->nullable();
            $table->unsignedBigInteger('use_rol_id');
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('use_uni_id')
                ->references('uni_id')
                ->on('unidade')
                ->onDelete('set null');

            $table->foreign('use_rol_id')
                ->references('rol_id')
                ->on('role')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};