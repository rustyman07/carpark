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
        Schema::create('card_inventories', function (Blueprint $table) {
            $table->id();
            $table->string('QRCODE');
            $table->string('STATUS');
            $table->string('CARDNAME');
            $table->integer('NODAYS')->default(0);
            $table->decimal('PRICE',10,2)->default(0.00);
            $table->decimal('DISCOUNT',10,2)->default(0.00);
            $table->decimal('AMOUNT',10,2)->default(0.00);
            $table->decimal('BALANCE',10,2)->default(0.00);
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
        Schema::dropIfExists('card_inventories');
    }
};
