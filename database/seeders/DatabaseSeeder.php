<?php

namespace Database\Seeders;
use App\Models\SalesPerson;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'username' => 'test',
            'password' =>'123',
        ]);

             $this->call(CompanySeeder::class);
             $this->call(SalesPersonSeeder::class);
    }
}
