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
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('association1_id');
            $table->unsignedBigInteger('association2_id');
            $table->text('last_message')->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->unsignedBigInteger('last_sender_id')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('association1_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('association2_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('last_sender_id')->references('id')->on('users')->onDelete('set null');
            
            // Indexes
            $table->index('association1_id');
            $table->index('association2_id');
            $table->unique(['association1_id', 'association2_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
