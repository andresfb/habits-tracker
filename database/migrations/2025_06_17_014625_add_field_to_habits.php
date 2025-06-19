<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('habits', static function (Blueprint $table): void {
            $table->unsignedInteger('default_value')
                ->after('target_value')
                ->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('habits', static function (Blueprint $table): void {
            $table->dropColumn('default_value');
        });
    }
};
