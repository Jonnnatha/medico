<?php

namespace Database\Seeders;

use App\Models\Surgery;
use Illuminate\Database\Seeder;

class SurgerySeeder extends Seeder
{
    public function run(): void
    {
        Surgery::factory()->count(10)->create();
    }
}
