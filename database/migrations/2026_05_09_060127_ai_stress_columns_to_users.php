<?php
// database/migrations/2024_01_01_000003_add_ai_stress_columns_to_users_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('score')->default(0);
            $table->integer('total_attacks')->default(0);
            $table->integer('successful_breaks')->default(0);
            $table->json('badges')->nullable();
            $table->string('username')->unique()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['score', 'total_attacks', 'successful_breaks', 'badges', 'username']);
        });
    }
};