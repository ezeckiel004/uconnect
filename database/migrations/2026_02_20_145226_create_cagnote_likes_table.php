<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cagnote_likes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('cagnote_id');
            $table->timestamps();

            // Ensure a user can only like a cagnote once
            $table->unique(['user_id', 'cagnote_id']);

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('cagnote_id')->references('id')->on('cagnotes')->onDelete('cascade');

            // Indexes
            $table->index('user_id');
            $table->index('cagnote_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cagnote_likes');
    }
};
