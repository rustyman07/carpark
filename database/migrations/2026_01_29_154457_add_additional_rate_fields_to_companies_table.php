<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::table('companies', function (Blueprint $table) {
        $table->integer('additional_hour_block')->default(3)->after('hourly_billing_limit'); // 3 hours
        $table->decimal('additional_rate_per_block', 10, 2)->default(50.00)->after('additional_hour_block'); // ₱50
    });
}

public function down()
{
    Schema::table('companies', function (Blueprint $table) {
        $table->dropColumn(['additional_hour_block', 'additional_rate_per_block']);
    });
}
};
