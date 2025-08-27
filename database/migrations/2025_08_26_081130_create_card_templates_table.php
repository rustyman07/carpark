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
        Schema::create('card_templates', function (Blueprint $table) {
            $table->id();
             $table->string('CARDNAME',100);
            $table->integer('NOOFDAYS')->default(0);
            $table->decimal('PRICE',10,2)->default(0.00);
            $table->decimal('DISCOUNT',10,2)->nullable()->default(0.00);
            $table->decimal('AMOUNT',10,2)->default(0.00);
            $table->boolean('CANCELLED')->default(0);     
            $table->integer('CANCELLEDBY')->nullable();
            $table->dateTime('CANCELLEDDATETIME')->nullable();
            $table->integer('CREATEDBY')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('card_templates');
    }

};
