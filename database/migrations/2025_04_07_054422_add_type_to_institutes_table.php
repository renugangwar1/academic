<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('institutes', function (Blueprint $table) {
            $table->string('type')->default('chapter'); // or nullable(), based on your need
        });
    }
    
    public function down(): void
    {
        Schema::table('institutes', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
    
};