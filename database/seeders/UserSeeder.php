<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'super' => true,
            'name' => 'Admin',
            'slug' => 'admin',
            'email' => 'admin@admin.com',
        ]);

        User::factory()
            ->count(10)
            ->create(
                ['tenant_id' => 1]
            );

        User::factory()
            ->count(10)
            ->create();
    }
}
