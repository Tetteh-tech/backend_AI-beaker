<?php
// database/migrations/2024_01_01_000004_create_agent_sessions_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('session_token')->unique();
            $table->json('memory_context')->nullable();
            $table->json('active_agents')->nullable();
            $table->string('current_router')->default('central');
            $table->integer('stress_level')->default(0);
            $table->integer('queue_position')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
            
            $table->index('session_token');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_sessions');
    }
};