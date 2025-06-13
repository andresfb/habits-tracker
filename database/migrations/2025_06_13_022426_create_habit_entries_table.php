<?php

declare(strict_types=1);

use App\Models\Habit;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('habit_entries', static function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(Habit::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->unsignedInteger('value');
            $table->dateTime('logged_at')
                ->index('idx_habit_entries_logged_at');
            $table->string('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('habit_entries');
    }
};
