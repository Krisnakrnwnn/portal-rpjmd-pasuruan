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
        Schema::create('chat_analytics', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->integer('total_sessions')->default(0);
            $table->integer('total_messages')->default(0);
            $table->decimal('avg_messages_per_session', 8, 2)->default(0);
            $table->decimal('avg_response_time', 8, 2)->default(0);
            $table->integer('total_likes')->default(0);
            $table->integer('total_dislikes')->default(0);
            $table->text('top_questions')->nullable(); // JSON array
            $table->timestamps();
            
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_analytics');
    }
};
