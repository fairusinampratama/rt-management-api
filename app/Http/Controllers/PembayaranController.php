<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/pembayaran",
     *     tags={"Pembayaran"},
     *     summary="Get list of pembayaran",
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function index()
    {
        return Pembayaran::all();
    }

    /**
     * @OA\Post(
     *     path="/api/pembayaran",
     *     tags={"Pembayaran"},
     *     summary="Create new pembayaran",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"rumah_id","penghuni_id","bulan","tahun","jenis_iuran","jumlah","status"},
     *             @OA\Property(property="rumah_id", type="integer"),
     *             @OA\Property(property="penghuni_id", type="integer"),
     *             @OA\Property(property="bulan", type="integer"),
     *             @OA\Property(property="tahun", type="integer"),
     *             @OA\Property(property="jenis_iuran", type="string"),
     *             @OA\Property(property="jumlah", type="number"),
     *             @OA\Property(property="status", type="boolean"),
     *             @OA\Property(property="tanggal_bayar", type="string", format="date", nullable=true)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Created")
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rumah_id' => 'required|integer|exists:rumah,id',
            'penghuni_id' => 'required|integer|exists:penghuni,id',
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer',
            'jenis_iuran' => 'required|in:satpam,kebersihan',
            'jumlah' => 'required|numeric',
            'status' => 'required|boolean',
            'tanggal_bayar' => 'nullable|date',
        ]);
        $pembayaran = Pembayaran::create($validated);
        return response()->json($pembayaran, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/pembayaran/{id}",
     *     tags={"Pembayaran"},
     *     summary="Get detail pembayaran",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function show($id)
    {
        return Pembayaran::findOrFail($id);
    }

    /**
     * @OA\Put(
     *     path="/api/pembayaran/{id}",
     *     tags={"Pembayaran"},
     *     summary="Update pembayaran",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="rumah_id", type="integer"),
     *             @OA\Property(property="penghuni_id", type="integer"),
     *             @OA\Property(property="bulan", type="integer"),
     *             @OA\Property(property="tahun", type="integer"),
     *             @OA\Property(property="jenis_iuran", type="string"),
     *             @OA\Property(property="jumlah", type="number"),
     *             @OA\Property(property="status", type="boolean"),
     *             @OA\Property(property="tanggal_bayar", type="string", format="date", nullable=true)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Updated")
     * )
     */
    public function update(Request $request, $id)
    {
        $pembayaran = Pembayaran::findOrFail($id);
        $validated = $request->validate([
            'rumah_id' => 'sometimes|required|integer|exists:rumah,id',
            'penghuni_id' => 'sometimes|required|integer|exists:penghuni,id',
            'bulan' => 'sometimes|required|integer|min:1|max:12',
            'tahun' => 'sometimes|required|integer',
            'jenis_iuran' => 'sometimes|required|in:satpam,kebersihan',
            'jumlah' => 'sometimes|required|numeric',
            'status' => 'sometimes|required|boolean',
            'tanggal_bayar' => 'nullable|date',
        ]);
        $pembayaran->update($validated);
        return response()->json($pembayaran);
    }

    /**
     * @OA\Delete(
     *     path="/api/pembayaran/{id}",
     *     tags={"Pembayaran"},
     *     summary="Delete pembayaran",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=204, description="Deleted")
     * )
     */
    public function destroy($id)
    {
        $pembayaran = Pembayaran::findOrFail($id);
        $pembayaran->delete();
        return response()->json(null, 204);
    }
}
