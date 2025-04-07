<?php

use App\Models\Course;
use App\Models\Institute;
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
        Schema::create('test_data', function (Blueprint $table) {
            $table->id();
            $table->string('Stud_name')->nullable();
            $table->bigInteger('Stud_nchm_roll_number')->nullable();
            $table->foreignIdFor(Institute::class)->nullable();
            $table->string('subjects')->nullable();
            
            $table->foreign('institute_id')
            ->references('id')
            ->on('institutes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_data');
    }
};
