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
        Schema::table('students', function (Blueprint $table) {
            // Add foreign key with nullable to avoid issues with existing nulls
            $table->foreignId('enrollment_id')
                  ->nullable()
                  ->constrained('enrollments')
                  ->onDelete('cascade');

            // Redundant, 'constrained' already adds the index
            // $table->index('enrollment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['enrollment_id']);
            $table->dropColumn('enrollment_id');
        });
    }
};