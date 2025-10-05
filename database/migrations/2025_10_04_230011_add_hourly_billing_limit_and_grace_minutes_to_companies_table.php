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
        Schema::table('companies', function (Blueprint $table) {
            //
            $table->integer('hourly_billing_limit')
            ->nullable()
            ->after('rate_perday');

            $table->integer('grace_minutes')
            ->nullable()
            ->after('hourly_billing_limit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
         $table->dropColumn(['hourly_billing_limit', 'grace_minutes']);
        });
    }
};
