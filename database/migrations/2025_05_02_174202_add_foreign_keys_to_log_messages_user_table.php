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
        Schema::table('log_messages_user', function (Blueprint $table) {
            $table->foreign(['context'])->references(['id'])->on('context_log_messages_user');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('log_messages_user', function (Blueprint $table) {
            $table->dropForeign('log_messages_user_context_foreign');
        });
    }
};
