<?php

namespace App\Http\Controllers;

use App\Models\RumahPenghuni;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class RumahPenghuniController extends Controller
{
    /**
     * Display a paginated listing of the resource, with optional filtering.
     *
     * @OA\Get(
     *     path="/api/rumah-penghuni",
     *     tags={"RumahPenghuni"},
     *     summary="Get paginated list of rumah-penghuni assignments",
     *     @OA\Parameter(name="page", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="rumah_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="penghuni_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="search", in="query", description="Search by penghuni name or rumah number", @OA\Schema(type="string")),
     *     @OA\Parameter(name="sort_by", in="query", description="Sort field", @OA\Schema(type="string", enum={"id", "tanggal_masuk", "tanggal_keluar"})),
     *     @OA\Parameter(name="sort_order", in="query", description="Sort order", @OA\Schema(type="string", enum={"asc", "desc"}, default="desc")),
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function index(Request $request)
    {
        // Support pagination and filtering by rumah_id or penghuni_id
        $query = RumahPenghuni::with(['rumah', 'penghuni']);

        // Filter by rumah_id
        if ($request->has('rumah_id')) {
            $query->where('rumah_id', $request->get('rumah_id'));
        }

        // Filter by penghuni_id
        if ($request->has('penghuni_id')) {
            $query->where('penghuni_id', $request->get('penghuni_id'));
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('penghuni', function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%");
            })->orWhereHas('rumah', function ($q) use ($search) {
                $q->where('nomor_rumah', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'tanggal_masuk');
        $sortOrder = $request->get('sort_order', 'desc');
        
        // Validate sort fields
        $allowedSortFields = ['id', 'tanggal_masuk', 'tanggal_keluar'];
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'tanggal_masuk';
        }
        
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->get('per_page', 20);
        $perPage = min(max($perPage, 1), 100); // Limit between 1 and 100
        
        $result = $query->paginate($perPage);

        return response()->json($result);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
        $validated = $request->validate([
            'rumah_id' => 'required|integer|exists:rumah,id',
            'penghuni_id' => 'required|integer|exists:penghuni,id',
            'tanggal_masuk' => 'required|date|after_or_equal:2010-01-01|before_or_equal:2030-12-31',
            'tanggal_keluar' => 'nullable|date',
        ]);
        $rumahPenghuni = RumahPenghuni::create($validated);
            $rumahPenghuni->load(['rumah', 'penghuni']);
        return response()->json($rumahPenghuni, 201);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return RumahPenghuni::with(['rumah', 'penghuni'])->findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $rumahPenghuni = RumahPenghuni::findOrFail($id);
        $validated = $request->validate([
            'rumah_id' => 'sometimes|required|integer|exists:rumah,id',
            'penghuni_id' => 'sometimes|required|integer|exists:penghuni,id',
            'tanggal_masuk' => 'sometimes|required|date|after_or_equal:2010-01-01|before_or_equal:2030-12-31',
            'tanggal_keluar' => 'nullable|date',
        ]);
        $rumahPenghuni->update($validated);
        return response()->json($rumahPenghuni);
    }

    /**
     * Get a complete list of all assignments for filtering purposes.
     *
     * @OA\Get(
     *     path="/api/rumah-penghuni/all",
     *     tags={"RumahPenghuni"},
     *     summary="Get all rumah-penghuni assignments (not paginated)",
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function all()
    {
        return RumahPenghuni::all();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $rumahPenghuni = RumahPenghuni::findOrFail($id);
        $rumahPenghuni->delete();
        return response()->json(null, 204);
    }
}
