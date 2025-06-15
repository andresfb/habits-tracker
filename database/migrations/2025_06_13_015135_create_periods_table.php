<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('periods', static function (Blueprint $table): void {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->unsignedInteger('interval_days')->default(1);
            $table->timestamps();
        });

        DB::table('periods')->insert([
            ['name' => 'Day', 'slug' => 'day', 'interval_days' => 1,'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Week', 'slug' => 'week', 'interval_days' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Month', 'slug' => 'month', 'interval_days' => 30, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Quarter', 'slug' => 'quarter', 'interval_days' => 90, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Year', 'slug' => 'year', 'interval_days' => 365, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('periods');
    }
};
