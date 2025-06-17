<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitations', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')
                ->nullable()
                ->default(null);
            $table->string('name');
            $table->string('email')->unique();
            $table->string('token', 40)->unique();
            $table->dateTime('registered_at')
                ->nullable()
                ->default(null);
            $table->softDeletes();
            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitations');
    }
};
