<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('selfs', function (Blueprint $table) {
            $table->id('sel_id');
            $table->string('sel_name');
            $table->string('sel_pdv_ip');
            $table->string('sel_rtsp_url');
            $table->boolean('sel_status')->default(true);
            $table->unsignedBigInteger('sel_uni_id');
            $table->timestamps();

            $table->foreign('sel_uni_id')
                ->references('uni_id')
                ->on('unidade')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('selfs');
    }
};