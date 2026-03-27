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
            $table->string('motor_rate')->nullable()->after('additional_rate_per_block');
            $table->decimal('motor_rate_perhour', 10, 2)->nullable()->after('motor_rate');
            $table->decimal('motor_rate_perday', 10, 2)->nullable()->after('motor_rate_perhour');
            $table->integer('motor_hourly_billing_limit')->nullable()->after('motor_rate_perday');
            $table->integer('motor_grace_minutes')->nullable()->after('motor_hourly_billing_limit');
            $table->integer('motor_additional_hour_block')->nullable()->after('motor_grace_minutes');
            $table->decimal('motor_additional_rate_per_block', 10, 2)->nullable()->after('motor_additional_hour_block');
        });
            //
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
             $table->dropColumn([
                'motor_rate',
                'motor_rate_perhour',
                'motor_rate_perday',
                'motor_hourly_billing_limit',
                'motor_grace_minutes',
                'motor_additional_hour_block',
                'motor_additional_rate_per_block',
            ]);
            //
        });
    }
};
