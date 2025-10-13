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
        Schema::create('card_inventory_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('card_inventory_id');
            $table->unsignedBigInteger('card_template_id');
             $table->uuid('uuid')->nullable()->unique();
            $table->string('card_number')->unique();
            $table->string('qr_code_hash');
            $table->string('status');
            $table->string('card_name');    
            $table->integer('no_of_days')->default(1);
            $table->decimal('price',10,2)->default(0.00);
            $table->decimal('discount',10,2)->nullable()->default(0.00);
            $table->decimal('amount',10,2)->default(0.00);
            $table->decimal('balance',10,2)->default(0.00);
            $table->boolean('cancelled')->default(0);     
            $table->integer('cancelled_by')->nullable();
            $table->dateTime('cancelled_datetime')->nullable();
            $table->dateTime('valid_until')->nullable();
            $table->integer('created_by')->nullable();
             $table->softDeletes();
            $table->timestamps();
            

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('card_inventory_details');
    }
};
