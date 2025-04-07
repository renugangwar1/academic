<?php

use App\Models\Course;
use App\Models\Institute;
use App\Models\Student;
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
        Schema::create('diploma_results', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Student::class);
            $table->foreignIdFor(Course::class);
            $table->string('Stud_batch')->nullable();
            $table->integer('Stud_semester')->nullable();
            $table->string('Stud_academic_year')->nullable();
            $table->foreignIdFor(Institute::class)->nullable();
            
            $table->json('Mid_marks')->nullable();
            $table->json('End_marks')->nullable();
            $table->json('Marks_total')->nullable();
            
            $table->integer('Grand_Total')->nullable();
            $table->double('Total_Percentage',6,2)->nullable();
            $table->string('Result')->nullable();
            $table->string('Reappear_subject')->nullable();
            $table->integer('Reappear_subject_count')->nullable();

            $table->json('Mid_apear_status')->nullable();
            $table->json('End_apear_status')->nullable();
            $table->date('Result_date')->nullable();
            
            $table->ipAddress('system');
            
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('student_id')
            ->references('id')
            ->on('students');

            $table->foreign('course_id')
            ->references('id')
            ->on('courses');

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
        Schema::dropIfExists('diploma_results');
    }
};
