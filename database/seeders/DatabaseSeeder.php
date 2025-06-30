<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run seeders in order of dependencies
        $this->call([
            UserSeeder::class,
            RumahSeeder::class,
            PenghuniSeeder::class,
            RumahPenghuniSeeder::class,
            PembayaranSeeder::class,
            PengeluaranSeeder::class,
        ]);
    }
}
