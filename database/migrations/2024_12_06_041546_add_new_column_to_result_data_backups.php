<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\StudentHistory;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('result_data_backups', function (Blueprint $table) {
            $table->foreignIdFor(StudentHistory::class)->nullable();

            $table->foreign('student_history_id')
            ->references('id')
            ->on('student_histories');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('result_data_backups', function (Blueprint $table) {
            $table->dropColumn(['student_history_id']);
        });
    }
};
