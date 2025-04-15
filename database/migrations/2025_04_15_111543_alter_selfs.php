<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('selfs', function (Blueprint $table) {
            // Remover a coluna antiga de RTSP URL
            $table->dropColumn('sel_rtsp_url');

            // Adicionar novas colunas
            $table->string('sel_ip_dvr')->nullable();
            $table->string('sel_username_dvr')->nullable();
            $table->string('sel_password_dvr')->nullable();
            $table->string('sel_canal_camera')->nullable();
            $table->integer('sel_porta_dvr')->nullable();
            $table->string('sel_rtsp_path')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('selfs', function (Blueprint $table) {
            // Reverter as alterações
            $table->string('sel_rtsp_url');

            $table->dropColumn([
                'sel_ip_dvr',
                'sel_username_dvr', 
                'sel_password_dvr', 
                'sel_canal_camera', 
                'sel_porta_dvr',
                'sel_rtsp_path'
            ]);
        });
    }
};