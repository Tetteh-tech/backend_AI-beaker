<?php
// database/migrations/2024_01_01_000002_create_prompts_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prompts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('content');
            $table->string('type')->default('general');
            $table->string('agent_target')->nullable();
            $table->float('response_time')->nullable();
            $table->integer('tokens_used')->nullable();
            $table->float('ai_confidence')->nullable();
            $table->boolean('caused_contradiction')->default(false);
            $table->boolean('memory_break')->default(false);
            $table->json('ai_response')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prompts');
    }
};