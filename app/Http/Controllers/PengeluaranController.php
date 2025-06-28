<?php

namespace App\Http\Controllers;

use App\Models\Pengeluaran;
use Illuminate\Http\Request;

class PengeluaranController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/pengeluaran",
     *     tags={"Pengeluaran"},
     *     summary="Get list of pengeluaran",
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function index()
    {
        return Pengeluaran::all();
    }

    /**
     * @OA\Post(
     *     path="/api/pengeluaran",
     *     tags={"Pengeluaran"},
     *     summary="Create new pengeluaran",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nama_pengeluaran","jumlah","bulan","tahun","tanggal_pengeluaran"},
     *             @OA\Property(property="nama_pengeluaran", type="string"),
     *             @OA\Property(property="jumlah", type="number"),
     *             @OA\Property(property="bulan", type="integer"),
     *             @OA\Property(property="tahun", type="integer"),
     *             @OA\Property(property="tanggal_pengeluaran", type="string", format="date"),
     *             @OA\Property(property="keterangan", type="string", nullable=true)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Created")
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pengeluaran' => 'required|string',
            'jumlah' => 'required|numeric',
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer',
            'tanggal_pengeluaran' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);
        $pengeluaran = Pengeluaran::create($validated);
        return response()->json($pengeluaran, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/pengeluaran/{id}",
     *     tags={"Pengeluaran"},
     *     summary="Get detail pengeluaran",
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
        return Pengeluaran::findOrFail($id);
    }

    /**
     * @OA\Put(
     *     path="/api/pengeluaran/{id}",
     *     tags={"Pengeluaran"},
     *     summary="Update pengeluaran",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nama_pengeluaran", type="string"),
     *             @OA\Property(property="jumlah", type="number"),
     *             @OA\Property(property="bulan", type="integer"),
     *             @OA\Property(property="tahun", type="integer"),
     *             @OA\Property(property="tanggal_pengeluaran", type="string", format="date"),
     *             @OA\Property(property="keterangan", type="string", nullable=true)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Updated")
     * )
     */
    public function update(Request $request, $id)
    {
        $pengeluaran = Pengeluaran::findOrFail($id);
        $validated = $request->validate([
            'nama_pengeluaran' => 'sometimes|required|string',
            'jumlah' => 'sometimes|required|numeric',
            'bulan' => 'sometimes|required|integer|min:1|max:12',
            'tahun' => 'sometimes|required|integer',
            'tanggal_pengeluaran' => 'sometimes|required|date',
            'keterangan' => 'nullable|string',
        ]);
        $pengeluaran->update($validated);
        return response()->json($pengeluaran);
    }

    /**
     * @OA\Delete(
     *     path="/api/pengeluaran/{id}",
     *     tags={"Pengeluaran"},
     *     summary="Delete pengeluaran",
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
        $pengeluaran = Pengeluaran::findOrFail($id);
        $pengeluaran->delete();
        return response()->json(null, 204);
    }
}
