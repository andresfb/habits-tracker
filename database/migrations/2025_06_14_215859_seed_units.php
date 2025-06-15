<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('units')->insert([
            ['name' => 'Times', 'slug' => 'times', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pages',  'slug' => 'pages',  'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cups', 'slug' => 'cups', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Steps', 'slug' => 'steps', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pounds', 'slug' => 'pounds', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Words', 'slug' => 'words', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
};
