<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Student;
use App\Models\Course;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reappear_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Student::class);
            $table->foreignIdFor(Course::class);
            $table->integer('semester');
            $table->string('batch');
            $table->string('Reappear_applaid');
            $table->json('Reappear_fee');
            $table->integer('Reappear_late_fee');
            $table->boolean('Reappear_payment_status');
            $table->ipAddress('system');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('student_id')
            ->references('id')
            ->on('students');

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
        Schema::dropIfExists('reappear_applications');
    }
};
