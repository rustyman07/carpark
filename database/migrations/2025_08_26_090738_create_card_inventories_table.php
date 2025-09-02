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
    $table->unsignedBigInteger('card_template_id')->nullable(); // foreign key to templates?
    $table->string('card_name');
    $table->integer('no_of_cards')->default(1);
    $table->integer('no_of_days')->default(1);
    $table->decimal('price', 10, 2)->default(0.00);
    $table->decimal('discount', 10, 2)->nullable()->default(0.00);
    $table->decimal('amount', 10, 2)->default(0.00);
    $table->boolean('cancelled')->default(0);     
    $table->unsignedBigInteger('cancelled_by')->nullable();
    $table->dateTime('cancelled_datetime')->nullable();
    $table->unsignedBigInteger('created_by')->nullable();
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
