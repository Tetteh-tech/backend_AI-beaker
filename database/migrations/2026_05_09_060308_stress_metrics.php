<?php
// database/migrations/2024_01_01_000006_create_stress_metrics_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stress_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('agent_sessions')->onDelete('cascade');
            $table->integer('active_users_count');
            $table->integer('queue_length');
            $table->float('avg_response_time');
            $table->float('system_load');
            $table->json('agent_stats')->nullable();
            $table->timestamp('recorded_at');
            $table->timestamps();
            
            $table->index(['recorded_at', 'session_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stress_metrics');
    }
};