<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlertaLogsTable extends Migration
{
    public function up()
    {
        Schema::create('alerta_logs', function (Blueprint $table) {
            $table->id();
            $table->string('alert_origin'); // Código do PDV ou sel_id
            $table->unsignedBigInteger('alert_resolved_by')->nullable(); // ID do usuário
            $table->integer('position')->nullable(); // Posição do quadrante
            $table->timestamps(); // Isso cria created_at e updated_at
            $table->timestamp('closed_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('alerta_logs');
    }
}