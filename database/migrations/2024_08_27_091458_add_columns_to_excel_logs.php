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
        Schema::table('excel_logs', function (Blueprint $table) {
            $table->string('Tearm')->nullable();
            $table->string('Batch')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('excel_logs', function (Blueprint $table) {
            $table->dropColumn(['Tearm', 'Batch']);
        });
    }
};
