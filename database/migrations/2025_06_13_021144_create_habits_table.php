<?php

declare(strict_types=1);

use App\Models\Category;
use App\Models\Period;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('habits', static function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(User::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignIdFor(Category::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignIdFor(Unit::class)
                ->constrained()
                ->restrictOnDelete();
            $table->foreignIdFor(Period::class)
                ->constrained()
                ->restrictOnDelete();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->text('description')->nullable();
            $table->unsignedInteger('target_value')->default(0);
            $table->tinyInteger('allow_multiple_times')->default(1);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('habits');
    }
};
