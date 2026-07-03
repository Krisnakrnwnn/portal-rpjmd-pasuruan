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
        \App\Models\Stat::updateOrCreate(
            ['key' => 'gemini_model'],
            [
                'value' => 'gemini-2.5-flash',
                'label' => 'Model AI Chatbot'
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \App\Models\Stat::where('key', 'gemini_model')->delete();
    }
};
