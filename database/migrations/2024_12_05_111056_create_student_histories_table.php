<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Institute;
use App\Models\Course;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('student_histories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('student_id');
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('NCHMCT_Rollnumber')->unique();
            $table->string('JNU_Rollnumber')->unique();
            $table->string('batch')->nullable();
            $table->json('optionalSubject')->nullable();
            $table->ipAddress('system');
            $table->string('created_at')->nullable();
            $table->string('updated_at')->nullable();
            $table->foreignIdFor(Institute::class)->nullable();
            $table->foreignIdFor(Course::class)->nullable();
            $table->softDeletes();
            
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
        Schema::dropIfExists('student_histories');
    }
};
