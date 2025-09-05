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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade');
            $table->foreignId('sales_person_id')->nullable()->constrained('sales_people')->onDelete('set null');
            $table->decimal('amount', 10, 2)->default(0.00);         
            $table->string('payment_method', 50);     
            $table->string('transaction_ref')->nullable(); // receipt / reference no
            $table->dateTime('paid_at');              // when payment happened
            $table->enum('status', ['PAID','UNPAID','REFUNDED'])->default('PAID');
            $table->text('remarks')->nullable();      // optional notes
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
