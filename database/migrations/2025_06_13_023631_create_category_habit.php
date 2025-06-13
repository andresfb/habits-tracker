<?php

use App\Models\Category;
use App\Models\Habit;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_habit', static function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Category::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignIdFor(Habit::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_habit');
    }
};
