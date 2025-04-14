<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogMessageUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_messages_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('creator');
            $table->string('creator_username');
            $table->foreignId('context')->constrained('context_log_messages_user');
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
}
