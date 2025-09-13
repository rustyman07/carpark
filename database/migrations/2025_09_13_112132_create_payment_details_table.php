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
        Schema::create('payment_details', function (Blueprint $table) {
            $table->id();
          $table->unsignedBigInteger('header_id');
            $table->unsignedBigInteger('card_id');
            $table->string('card_name');
            $table->string('card_number');
            $table->integer('no_of_days');
            $table->decimal('discount',10,2)->nullable()->default(0.00);
            $table->decimal('balance',10,2)->default(0.00);
            $table->decimal('amount',10,2)->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_details');
    }
};
