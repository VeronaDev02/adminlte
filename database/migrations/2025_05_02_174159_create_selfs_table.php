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
        Schema::create('selfs', function (Blueprint $table) {
            $table->bigIncrements('sel_id');
            $table->string('sel_name');
            $table->string('sel_pdv_ip');
            $table->boolean('sel_status')->default(true);
            $table->bigInteger('sel_uni_id');
            $table->timestamps();
            $table->string('sel_dvr_ip')->nullable();
            $table->string('sel_dvr_username')->nullable();
            $table->string('sel_dvr_password')->nullable();
            $table->string('sel_camera_canal')->nullable();
            $table->integer('sel_dvr_porta')->nullable();
            $table->text('sel_rtsp_path')->nullable();
            $table->string('sel_pdv_codigo', 3)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('selfs');
    }
};
