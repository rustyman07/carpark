<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Company;
use Illuminate\Support\Facades\DB;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Company::factory()->create([
        //     'name' => 'Cerebro Diagnostic',
        //     'address' => 'Zone 4 Cugman Cagayan de oro City',
        //     'post_paid_rate' => '500.00'
        // ]);
        // Company::factory()->count(1)->create();

        DB::table('companies')->insert([
    [
        'name'           => 'Cerebro Diagnostic System',
        'address'        => 'Zone 4 Cugman Cagayan de oro City',
        'contact'        => '09123456789',
        'post_paid_rate' => 500.00,
        'created_at'     => now(),
        'updated_at'     => now(),
    ],
]);



    }
}
