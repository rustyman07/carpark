<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. Morning, Afternoon, Night
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_active')->default(true); // optional: to enable/disable shifts
            $table->timestamps();
        });

        // Optional: Add shift_id column to users table
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('shift_id')->nullable()->constrained('shifts')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['shift_id']);
            $table->dropColumn('shift_id');
        });

        Schema::dropIfExists('shifts');
    }
};
