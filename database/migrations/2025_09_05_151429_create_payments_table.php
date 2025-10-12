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

            // Foreign keys
            // $table->foreignId('card_id')
            //       ->nullable()
            //       ->constrained('card_inventories') // references id on card_inventory
            //       ->cascadeOnDelete();

            $table->foreignId('ticket_id')
                  ->nullable()
                  ->constrained('tickets') // references id on tickets
                  ->cascadeOnDelete();
            // $table->foreignId('sales_person_id')->nullable()->constrained('sales_people')->onDelete('set null');
            // Payment details
            // $table->string('qr_code')->nullable();
            $table->decimal('amount', 8, 2)->default(0.00);
            $table->string('customer')->nullable();
            $table->decimal('total_amount', 8, 2)->default(0.00);
            $table->decimal('change', 8, 2)->default(0.00);
            $table->enum('payment_type', ['Card', 'Ticket']);
            $table->enum('payment_method', ['Cash', 'Card', 'Gcash']);
            $table->integer('days_deducted')->nullable();
            $table->enum('status', ['Pending', 'Paid', 'Failed', 'Refunded'])->default('Pending');
            $table->integer('processed_by')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('remarks')->nullable();    
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
