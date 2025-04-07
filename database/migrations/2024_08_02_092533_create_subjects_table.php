<?php

use App\Models\Course;
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
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Course::class);
            $table->string('Subject_name')->nullable();
            $table->string('Subject_code')->nullable();
            
            $table->string('Subject_type')->nullable();
            $table->integer('Semester')->nullable();
            $table->double('Reappear_fee',10,2)->nullable();
           
            $table->boolean('Optional_subject')->nullable();
            $table->integer('Credit')->nullable();
            $table->integer('Mid_max_mark')->nullable();
            $table->double('Mid_pass_mark')->nullable();
            $table->integer('End_max_mark')->nullable();
            $table->double('End_pass_mark')->nullable();
            $table->ipAddress('system');
            
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('course_id')
            ->references('id')
            ->on('courses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
