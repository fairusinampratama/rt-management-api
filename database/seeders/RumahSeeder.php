<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Rumah;

class RumahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data safely (handles foreign key constraints)
        try {
            Rumah::query()->delete();
        } catch (\Exception $e) {
            $this->command->warn('Could not clear existing rumah data due to foreign key constraints. Continuing...');
        }

        // Create 20 houses: A1-A20
        for ($i = 1; $i <= 20; $i++) {
            $nomor = 'A' . $i;
            $status = $i <= 15 ? 1 : 0; // 1 = Tetap (occupied), 0 = Kosong/Kontrak
            Rumah::create([
                'nomor_rumah' => $nomor,
                'status' => $status,
            ]);
        }
    }
}
