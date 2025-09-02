<?php

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
        Schema::create('companies', function (Blueprint $table) {
          $table->id();
        $table->string('name'); 
        // $table->string('city', 100)->nullable();
        // $table->string('province', 100)->nullable();
        // $table->string('barangay', 150)->nullable();
        // $table->string('street', 255)->nullable();
        $table->string('contact', 20)->nullable();
        $table->string('address', 255)->nullable();
        $table->decimal('post_paid_rate', 10, 2)->default(0.00);

        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
