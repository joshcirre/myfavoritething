<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('feeds', function (Blueprint $table) {
            $table->boolean('notifications_sent')->default(false);
        });
    }

    public function down()
    {
        Schema::table('feeds', function (Blueprint $table) {
            $table->dropColumn('notifications_sent');
        });
    }
};
