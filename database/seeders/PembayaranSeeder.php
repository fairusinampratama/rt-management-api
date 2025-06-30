<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pembayaran;
use App\Models\Rumah;
use App\Models\Penghuni;
use App\Models\RumahPenghuni;
use Carbon\Carbon;

class PembayaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing data
        $rumahs = Rumah::all();
        $penghunis = Penghuni::all();
        $assignments = RumahPenghuni::all();

        if ($rumahs->isEmpty() || $penghunis->isEmpty() || $assignments->isEmpty()) {
            $this->command->error('Please run RumahSeeder, PenghuniSeeder, and RumahPenghuniSeeder first!');
            return;
        }

        $paymentTypes = [
            'satpam' => 100000,
            'kebersihan' => 15000,
        ];

        $months = [
            'Januari' => 1, 'Februari' => 2, 'Maret' => 3, 'April' => 4,
            'Mei' => 5, 'Juni' => 6, 'Juli' => 7, 'Agustus' => 8,
            'September' => 9, 'Oktober' => 10, 'November' => 11, 'Desember' => 12
        ];

        $pembayaranData = [];
        $recordCount = 0;

        // Generate data for 2025 only (January - December 2025)
        $year = 2025;
        
        for ($month = 1; $month <= 12; $month++) {
            // For each month, create payments for each assignment
            foreach ($assignments as $assignment) {
                // Skip if resident moved out before this month
                if ($assignment->tanggal_keluar && 
                    Carbon::parse($assignment->tanggal_keluar)->format('Y-m') <= "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT)) {
                    continue;
                }

                // Skip if resident moved in after this month
                if (Carbon::parse($assignment->tanggal_masuk)->format('Y-m') > "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT)) {
                    continue;
                }

                // Create both satpam and kebersihan payments for each month
                foreach ($paymentTypes as $jenis => $jumlah) {
                    // Determine payment status (80% paid, 20% unpaid)
                    $status = (rand(1, 100) <= 80) ? 1 : 0;
                    
                    // Generate payment date (1-15th of the month for paid payments)
                    $tanggalBayar = null;
                    if ($status == 1) {
                        $day = rand(1, 15);
                        $tanggalBayar = "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-" . str_pad($day, 2, '0', STR_PAD_LEFT);
                    }

                    $pembayaranData[] = [
                        'rumah_id' => $assignment->rumah_id,
                        'penghuni_id' => $assignment->penghuni_id,
                        'bulan' => $month,
                        'tahun' => $year,
                        'jenis_iuran' => $jenis,
                        'jumlah' => $jumlah,
                        'status' => $status,
                        'tanggal_bayar' => $tanggalBayar,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    $recordCount++;
                }
            }
        }

        // Insert data in chunks to avoid memory issues
        foreach (array_chunk($pembayaranData, 100) as $chunk) {
            Pembayaran::insert($chunk);
        }
        
        // Show summary
        $paidCount = Pembayaran::where('status', 1)->count();
        $unpaidCount = Pembayaran::where('status', 0)->count();
        $totalAmount = Pembayaran::sum('jumlah');
    }
}
