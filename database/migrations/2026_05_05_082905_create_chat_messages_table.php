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
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->string('session_id', 255);
            $table->string('role', 10); // 'user' or 'model'
            $table->text('message');
            $table->timestamps();
            
            $table->index('session_id');
            $table->index('created_at');
            
            $table->foreign('session_id')
                  ->references('session_id')
                  ->on('chat_sessions')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
