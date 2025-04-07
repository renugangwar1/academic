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
        Schema::create('reappear_settings', function (Blueprint $table) {
            $table->id();
            // $table->string('course');
            $table->unsignedBigInteger('course_id');
            $table->integer('semester');
            $table->string('batch');
            $table->date('Reappear_from_date');
            $table->date('Reappear_to_date');
            $table->date('Reappear_late_fee_date');
            $table->integer('Reappear_late_fee');
            $table->ipAddress('system');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reappear_settings');
    }
};
