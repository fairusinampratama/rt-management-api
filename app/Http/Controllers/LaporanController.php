<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LaporanController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/laporan/summary",
     *     tags={"Laporan"},
     *     summary="Get summary pemasukan, pengeluaran, dan saldo per bulan/tahun",
     *     @OA\Parameter(
     *         name="bulan",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="tahun",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function summary(Request $request)
    {
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');

        $total_pemasukan = (float) \App\Models\Pembayaran::where('status', 1)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->sum('jumlah');

        $total_pengeluaran = (float) \App\Models\Pengeluaran::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->sum('jumlah');

        $saldo = $total_pemasukan - $total_pengeluaran;

        return response()->json([
            'bulan' => $bulan,
            'tahun' => $tahun,
            'total_pemasukan' => $total_pemasukan,
            'total_pengeluaran' => $total_pengeluaran,
            'saldo' => $saldo,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/laporan/grafik",
     *     tags={"Laporan"},
     *     summary="Get grafik pemasukan dan pengeluaran per bulan dalam 1 tahun",
     *     @OA\Parameter(
     *         name="tahun",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function grafik(Request $request)
    {
        $tahun = $request->input('tahun');
        $result = [];

        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $pemasukan = \App\Models\Pembayaran::where('status', 1)
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->sum('jumlah');
            $pengeluaran = \App\Models\Pengeluaran::where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->sum('jumlah');
            $result[] = [
                'bulan' => $bulan,
                'pemasukan' => $pemasukan,
                'pengeluaran' => $pengeluaran,
            ];
        }
        return response()->json($result);
    }

    /**
     * @OA\Get(
     *     path="/api/laporan/pemasukan",
     *     tags={"Laporan"},
     *     summary="Get tabel pemasukan per bulan/tahun",
     *     @OA\Parameter(
     *         name="bulan",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="tahun",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function pemasukan(Request $request)
    {
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $data = \App\Models\Pembayaran::with(['rumah', 'penghuni'])
            ->where('status', 1)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get();
        return response()->json($data);
    }

    /**
     * @OA\Get(
     *     path="/api/laporan/pengeluaran",
     *     tags={"Laporan"},
     *     summary="Get tabel pengeluaran per bulan/tahun",
     *     @OA\Parameter(
     *         name="bulan",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="tahun",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function pengeluaran(Request $request)
    {
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $data = \App\Models\Pengeluaran::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get();
        return response()->json($data);
    }

    /**
     * @OA\Get(
     *     path="/api/laporan/saldo",
     *     tags={"Laporan"},
     *     summary="Get saldo berjalan per bulan dalam 1 tahun",
     *     @OA\Parameter(
     *         name="tahun",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function saldo(Request $request)
    {
        $tahun = $request->input('tahun');
        $result = [];
        $saldo = 0;
        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $pemasukan = \App\Models\Pembayaran::where('status', 1)
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->sum('jumlah');
            $pengeluaran = \App\Models\Pengeluaran::where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->sum('jumlah');
            $saldo += ($pemasukan - $pengeluaran);
            $result[] = [
                'bulan' => $bulan,
                'saldo' => $saldo,
            ];
        }
        return response()->json($result);
    }
}
