<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invitations', static function (Blueprint $table): void {
            $table->unsignedTinyInteger('status')
                ->default(0)
                ->after('token');

            $table->index(['token', 'status'], 'idx_token_status');
        });
    }

    public function down(): void
    {
        Schema::table('invitations', static function (Blueprint $table): void {
            $table->dropIndex('idx_token_status');

            $table->dropColumn('status');
        });
    }
};
