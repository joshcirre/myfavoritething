<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('feed_user', function (Blueprint $table) {
            $table->foreignId('favorite_post_id')->nullable()->constrained('posts')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('feed_user', function (Blueprint $table) {
            $table->dropForeign(['favorite_post_id']);
            $table->dropColumn('favorite_post_id');
        });
    }
};
