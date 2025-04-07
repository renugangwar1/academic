<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Institute;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('enrollments', function (Blueprint $table) {
        $table->id();
        $table->string('year_of_admission');
        $table->string('institute_name');
        // $table->foreignIdfor(Institute::class);
        $table->string('chapter_name');
      
        $table->string('programme_of_study');
        $table->string('student_name_en');
        $table->string('student_name_hi');
        $table->date('date_of_birth');
        $table->string('category');
        $table->string('pwbd_category');
        $table->string('hh_oh_vh');
        $table->string('percentage')->nullable();
        $table->string('father_name');
        $table->string('father_mobile');
        $table->string('mother_name');
        $table->string('guardian_name');
        $table->string('local_address');
        $table->string('permanent_address');
        $table->string('state_of_domicile');
        $table->string('nationality');
        $table->string('student_email');
        $table->string('student_mobile');
        $table->string('abc_id');
        $table->string('student_image');
        $table->string('nchm_roll_no');
        




        // 10th Records
       
        $table->string('board_10th')->nullable();
        $table->string('school_10th')->nullable();
        $table->string('year_10th')->nullable();
        $table->string('subject_10th')->nullable();
        $table->string('percentage_10th', 10)->nullable();
        // 12th Records
        $table->string('board_12th')->nullable();
        $table->string('school_12th')->nullable();
        $table->string('year_12th')->nullable();
        $table->string('subject_12th')->nullable();
        $table->string('percentage_12th', 10)->nullable();
        // Other Records
        $table->string('board_other')->nullable();
        $table->string('school_other')->nullable();
        $table->string('year_other')->nullable();
        $table->string('subject_other')->nullable();
        $table->string('percentage_other', 10)->nullable();
                $table->timestamps();
        $table->softDeletes();

        $table->foreignId('institute_id')->constrained('institutes')->onDelete('cascade');


       
    });
}

    


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};