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
        Schema::create('crawler_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('api_key_id')->nullable()->constrained()->onDelete('set null');
            $table->string('site');
            $table->text('keywords'); // JSON array
            $table->integer('matches_count')->default(0);
            $table->enum('status', ['success', 'error'])->default('success');
            $table->integer('execution_time')->default(0); // milliseconds
            $table->text('response_message')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index('user_id');
            $table->index('api_key_id');
            $table->index('created_at');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crawler_history');
    }
};


