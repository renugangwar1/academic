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
        Schema::table('reappear_settings', function (Blueprint $table) {
            //
            // $table->foreignIdFor(Course::class)->before('semester');
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
        Schema::table('reappear_settings', function (Blueprint $table) {
            $table->dropForeign(['course_id']); // Drop the foreign key constraint
            $table->dropColumn('course_id');
        });
    }
};
