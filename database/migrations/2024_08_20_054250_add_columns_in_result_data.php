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
        Schema::table('result_data', function (Blueprint $table) {
            $table->json('Mid_apear_status')->nullable();
            $table->json('End_apear_status')->nullable();
            $table->date('Compaile_date')->nullable();
            $table->date('Result_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('result_data', function (Blueprint $table) {
            $table->dropColumn(['Mid_apear_status', 'Compaile_date', 'End_apear_status', 'Result_date']);
        });
    }
};
