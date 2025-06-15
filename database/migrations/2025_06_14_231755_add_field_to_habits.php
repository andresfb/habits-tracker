<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('habits', static function (Blueprint $table): void {
            $table->unsignedSmallInteger('order_by')
                ->after('allow_multiple_times')
                ->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('habits', static function (Blueprint $table): void {
            $table->dropColumn('order_by');
        });
    }
};
