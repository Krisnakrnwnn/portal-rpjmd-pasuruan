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
        Schema::create('shared_messages', function (Blueprint $table) {
            $table->id();
            $table->string('message_id', 255);
            $table->text('message');
            $table->string('share_token', 255)->unique();
            $table->timestamp('expires_at');
            $table->timestamps();
            
            $table->index('share_token');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shared_messages');
    }
};
