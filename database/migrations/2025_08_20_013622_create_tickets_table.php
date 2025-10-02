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

            $table->string('ticket_no', 20);
            $table->string('plate_no', 20);
            $table->string ('mode_of_payment',100)->nullable();
            $table->integer ('days_parked')->nullable();    
            $table->integer('hours_parked')->nullable();
            $table->string('qr_code', 255)->nullable();
            $table->integer('park_year');
            $table->integer('park_month');
            $table->integer('park_day');
            $table->integer('park_hour');
            $table->integer('park_minute');
            $table->integer('park_second');
            $table->decimal('park_fee', 10, 2)->nullable();
            $table->integer('park_out_year')->nullable();
            $table->integer('park_out_month')->nullable();
            $table->integer('park_out_day')->nullable();
            $table->integer('park_out_hour')->nullable();
            $table->integer('park_out_minute')->nullable();
            $table->integer('park_out_second')->nullable();
            $table->boolean('is_park_out')->default(0);
            $table->integer('total_minutes')->nullable();
            $table->dateTime('park_datetime');
            $table->dateTime('park_out_datetime')->nullable();
            $table->string('remarks', 50)->nullable();
            $table->boolean('cancelled')->default(0);
            $table->integer('cancelled_by')->nullable();
            $table->dateTime('cancelled_datetime')->nullable();
            $table->string('slip_no', 100)->nullable();
            $table->integer('park_in_by')->nullable();
            $table->integer('park_out_by')->nullable();
            $table->integer('created_by')->nullable();
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
