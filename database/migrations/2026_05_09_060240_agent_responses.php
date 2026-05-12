<?php
// database/migrations/2024_01_01_000005_create_agent_responses_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('agent_sessions')->onDelete('cascade');
            $table->foreignId('prompt_id')->constrained()->onDelete('cascade');
            $table->string('agent_name');
            $table->text('response_content');
            $table->json('metadata')->nullable();
            $table->float('response_time');
            $table->integer('tokens_used');
            $table->float('confidence_score');
            $table->boolean('is_failed')->default(false);
            $table->string('failure_reason')->nullable();
            $table->timestamps();
            
            $table->index(['session_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_responses');
    }
};