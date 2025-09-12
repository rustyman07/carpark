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
        Schema::create('cards_transactions', function (Blueprint $table) {
        $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade');
            $table->string('ticket_no');
            $table->foreignId('card_id')->constrained('card_inventories')->onDelete('cascade');
            $table->string('card_number')->nullable();
            $table->string('card_name')->nullable();
            $table->integer('no_of_days')->default(1);
            $table->decimal('price',10,2)->default(0.00);
            $table->decimal('discount',10,2)->nullable()->default(0.00);
            $table->decimal('amount',10,2)->default(0.00);
            $table->integer('balance')->default(0);
            $table->boolean('cancelled')->default(0);     
            $table->integer('cancelled_by')->nullable();
            $table->dateTime('cancelled_datetime')->nullable();
            $table->integer('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cards_transactions');
    }
};
