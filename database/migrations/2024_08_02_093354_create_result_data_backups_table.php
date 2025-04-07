<?php

use App\Models\Course;
use App\Models\Institute;
use App\Models\ResultData;
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
        Schema::create('result_data_backups', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ResultData::class)->nullable();
            $table->foreignIdFor(Student::class)->nullable();
            $table->foreignIdFor(Course::class)->nullable();
            $table->string('Stud_batch')->nullable();
            $table->integer('Stud_semester')->nullable();
            $table->string('Stud_academic_year')->nullable();
            $table->foreignIdFor(Institute::class)->nullable();
            
            $table->longText('Mid_Reappear_subject')->nullable();
            $table->longText('Mid_Appear_subject')->nullable();
            $table->string('Mid_Result')->nullable();


            $table->json('Mid_marks')->nullable();
            $table->json('End_marks')->nullable();
            $table->json('Marks_total')->nullable();
            $table->json('Marks_grade')->nullable();
            $table->json('Marks_grade_point')->nullable();
            $table->json('Marks_credit')->nullable();
            $table->json('Marks_credit_point')->nullable();
            

            $table->integer('Grand_Total')->nullable();
            $table->string('Grand_Total_Grade')->nullable();
            $table->string('Grand_Credit_Point')->nullable();
            $table->double('Total_Percentage',6,2)->nullable();
            $table->string('End_Result')->nullable();
            $table->string('End_Reappear_subject')->nullable();
            $table->string('Total_Reappear_subject')->nullable();
            $table->integer('Reappear_subject_count')->nullable();
            $table->string('Optional_subject')->nullable();
            $table->string('End_Result_SGPA')->nullable();
            $table->string('End_Result_CGPA')->nullable();

            $table->ipAddress('system');
            
            $table->string('created_at')->nullable();
            $table->string('updated_at')->nullable();
            
            $table->softDeletes();

            $table->foreign('result_data_id')
            ->references('id')
            ->on('result_data');
            

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
        Schema::dropIfExists('result_data_backups');
    }
};
