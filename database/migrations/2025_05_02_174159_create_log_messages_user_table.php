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
        Schema::create('log_messages_user', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('creator');
            $table->string('creator_username');
            $table->bigInteger('context');
            $table->string('target')->nullable();
            $table->string('ip_origin');
            $table->string('mac_address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_messages_user');
    }
};
