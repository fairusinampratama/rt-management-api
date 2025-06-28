<?php

namespace App\Http\Controllers;

use App\Models\Rumah;
use Illuminate\Http\Request;

class RumahController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/rumah",
     *     tags={"Rumah"},
     *     summary="Get list of rumah",
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function index()
    {
        return Rumah::all();
    }

    /**
     * @OA\Post(
     *     path="/api/rumah",
     *     tags={"Rumah"},
     *     summary="Create new rumah",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nomor_rumah","status"},
     *             @OA\Property(property="nomor_rumah", type="string"),
     *             @OA\Property(property="status", type="string")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Created")
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_rumah' => 'required|string',
            'status' => 'required|in:terisi,kosong,dipesan',
        ]);
        $rumah = Rumah::create($validated);
        return response()->json($rumah, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/rumah/{id}",
     *     tags={"Rumah"},
     *     summary="Get detail rumah",
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
        return Rumah::findOrFail($id);
    }

    /**
     * @OA\Put(
     *     path="/api/rumah/{id}",
     *     tags={"Rumah"},
     *     summary="Update rumah",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nomor_rumah", type="string"),
     *             @OA\Property(property="status", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Updated")
     * )
     */
    public function update(Request $request, $id)
    {
        $rumah = Rumah::findOrFail($id);
        $validated = $request->validate([
            'nomor_rumah' => 'sometimes|required|string',
            'status' => 'sometimes|required|in:terisi,kosong,dipesan',
        ]);
        $rumah->update($validated);
        return response()->json($rumah);
    }

    /**
     * @OA\Delete(
     *     path="/api/rumah/{id}",
     *     tags={"Rumah"},
     *     summary="Delete rumah",
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
        $rumah = Rumah::findOrFail($id);
        $rumah->delete();
        return response()->json(null, 204);
    }
}
