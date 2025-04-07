<?php

use App\Models\User;
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
        Schema::create('excel_logs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('uploader_id');
            $table->string('excel_title');
            $table->string('excel_link');
            $table->string('UserName')->nullable();
            $table->ipAddress('system');
            $table->timestamps();        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('excel_logs');
    }
};
