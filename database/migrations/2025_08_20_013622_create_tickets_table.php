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
        Schema::create('tickets', function (Blueprint $table) {
         $table->id();

            $table->string('TICKETNO', 20);
            $table->string('PLATENO', 20);
            $table->string ('mode_of_payment',100)->nullable();
             $table->integer ('days_parked')->nullable();               
            $table->string('QRCODE', 255)->nullable();
            $table->integer('PARKYEAR');
            $table->integer('PARKMONTH');
            $table->integer('PARKDAY');
            $table->integer('PARKHOUR');
            $table->integer('PARKMINUTE');
            $table->integer('PARKSECOND');
            $table->decimal('PARKFEE', 18, 4)->nullable();
            $table->integer('PARKOUTYEAR')->nullable();
            $table->integer('PARKOUTMONTH')->nullable();
            $table->integer('PARKOUTDAY')->nullable();
            $table->integer('PARKOUTHOUR')->nullable();
            $table->integer('PARKOUTMINUTE')->nullable();
            $table->integer('PARKOUTSECOND')->nullable();
            $table->boolean('ISPARKOUT')->default(0);
            $table->integer('TOTALMINUTES')->nullable();
            $table->dateTime('PARKDATETIME');
            $table->dateTime('PARKOUTDATETIME')->nullable();
            $table->string('REMARKS', 50)->nullable();
            $table->boolean('CANCELLED')->default(0);
            $table->integer('CANCELLEDBY')->nullable();
            $table->dateTime('CANCELLEDDATETIME')->nullable();
            $table->string('SLIPNO', 100)->nullable();
            $table->integer('PARKINBY')->nullable();
            $table->integer('PARKOUTBY')->nullable();
            $table->integer('CREATEDBY')->nullable();
            $table->softDeletes();
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
