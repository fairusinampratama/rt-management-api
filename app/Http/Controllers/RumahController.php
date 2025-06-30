<?php

namespace App\Http\Controllers;

use App\Models\Rumah;
use App\Models\RumahPenghuni;
use Illuminate\Http\Request;

class RumahController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/rumah",
     *     tags={"Rumah"},
     *     summary="Get list of rumah",
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search term for nomor_rumah",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Sort field",
     *         @OA\Schema(type="string", enum={"id", "nomor_rumah", "status"})
     *     ),
     *     @OA\Parameter(
     *         name="sort_order",
     *         in="query",
     *         description="Sort order",
     *         @OA\Schema(type="string", enum={"asc", "desc"}, default="asc")
     *     ),
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function index(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string|max:50',
        ]);
        $query = Rumah::with(['currentResidents']);

        // Apply search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('nomor_rumah', 'like', "%{$search}%");
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'asc');
        
        // Validate sort fields
        $allowedSortFields = ['id', 'nomor_rumah', 'status'];
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'id';
        }
        
        $query->orderBy($sortBy, $sortOrder);

        // Apply pagination
        $perPage = $request->get('per_page', 10);
        $perPage = min(max($perPage, 1), 100); // Limit between 1 and 100

        return $query->paginate($perPage);
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
            'nomor_rumah' => 'required|string|min:1|max:10',
            'status' => 'required|in:0,1',
        ]);
        $rumah = Rumah::create($validated);
        return response()->json($rumah, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/rumah/{id}",
     *     tags={"Rumah"},
     *     summary="Get detail rumah with current residents",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function show($id)
    {
        $rumah = Rumah::with(['currentResidents'])->findOrFail($id);
        return $rumah;
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
            'nomor_rumah' => 'sometimes|required|string|min:1|max:10',
            'status' => 'sometimes|required|in:0,1',
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

    /**
     * Get paginated history of residents for a house
     *
     * @OA\Get(
     *     path="/api/rumah/{id}/history",
     *     tags={"Rumah"},
     *     summary="Get paginated resident history for a house",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="page", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function history(Request $request, $id)
    {
        $perPage = $request->get('per_page', 10);
        $history = RumahPenghuni::where('rumah_id', $id)
            ->with('penghuni')
            ->orderByDesc('tanggal_masuk')
            ->paginate($perPage);
        return response()->json($history);
    }
}
