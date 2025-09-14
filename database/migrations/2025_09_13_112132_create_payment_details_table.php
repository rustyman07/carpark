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
        $table->foreignId('payment_id')
        ->constrained('payments')
        ->cascadeOnDelete();
        $table->foreignId('card_id')->nullable()->constrained('card_inventory_details')->nullOnDelete();
        $table->string('card_number')->nullable();
        $table->string('qr_code')->nullable();
        $table->integer('no_of_days')->default(0);
        $table->decimal('discount',10,2)->nullable()->default(0.00);
        $table->decimal('balance', 10, 2)->nullable();
        $table->string('card_name')->nullable();
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
