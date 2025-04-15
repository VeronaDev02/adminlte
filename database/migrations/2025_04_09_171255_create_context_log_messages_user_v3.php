<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContextLogMessagesUserV3 extends Migration
{
    public function up()
    {
        Schema::create('context_log_messages_user', function (Blueprint $table) {
            $table->id();
            $table->string('context');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('context_log_messages_user');
    }
}
