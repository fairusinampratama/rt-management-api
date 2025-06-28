<?php

namespace App\Http\Controllers;

use App\Models\Penghuni;
use Illuminate\Http\Request;

class PenghuniController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/penghuni",
     *     tags={"Penghuni"},
     *     summary="Get list of penghuni",
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function index()
    {
        return Penghuni::all();
    }

    /**
     * @OA\Post(
     *     path="/api/penghuni",
     *     tags={"Penghuni"},
     *     summary="Create new penghuni",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nama_lengkap","status_penghuni","nomor_telepon","status_menikah"},
     *             @OA\Property(property="nama_lengkap", type="string"),
     *             @OA\Property(property="foto_ktp", type="string", nullable=true),
     *             @OA\Property(property="status_penghuni", type="boolean"),
     *             @OA\Property(property="nomor_telepon", type="string"),
     *             @OA\Property(property="status_menikah", type="boolean")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Created")
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string',
            'foto_ktp' => 'nullable|string',
            'status_penghuni' => 'required|boolean',
            'nomor_telepon' => 'required|string',
            'status_menikah' => 'required|boolean',
        ]);
        $penghuni = Penghuni::create($validated);
        return response()->json($penghuni, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/penghuni/{id}",
     *     tags={"Penghuni"},
     *     summary="Get detail penghuni",
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
        return Penghuni::findOrFail($id);
    }

    /**
     * @OA\Put(
     *     path="/api/penghuni/{id}",
     *     tags={"Penghuni"},
     *     summary="Update penghuni",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nama_lengkap", type="string"),
     *             @OA\Property(property="foto_ktp", type="string", nullable=true),
     *             @OA\Property(property="status_penghuni", type="boolean"),
     *             @OA\Property(property="nomor_telepon", type="string"),
     *             @OA\Property(property="status_menikah", type="boolean")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Updated")
     * )
     */
    public function update(Request $request, $id)
    {
        $penghuni = Penghuni::findOrFail($id);
        $validated = $request->validate([
            'nama_lengkap' => 'sometimes|required|string',
            'foto_ktp' => 'nullable|string',
            'status_penghuni' => 'sometimes|required|boolean',
            'nomor_telepon' => 'sometimes|required|string',
            'status_menikah' => 'sometimes|required|boolean',
        ]);
        $penghuni->update($validated);
        return response()->json($penghuni);
    }

    /**
     * @OA\Delete(
     *     path="/api/penghuni/{id}",
     *     tags={"Penghuni"},
     *     summary="Delete penghuni",
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
        $penghuni = Penghuni::findOrFail($id);
        $penghuni->delete();
        return response()->json(null, 204);
    }
}
