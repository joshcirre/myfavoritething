<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feed_user', function (Blueprint $table) {
            $table->id();
            $table->uuid('feed_id');
            $table->foreignId('user_id')->constrained('users');
            $table->foreign('feed_id')->references('id')->on('feeds')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['feed_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feed_user');
    }
};
