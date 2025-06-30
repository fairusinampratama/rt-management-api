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
     *     @OA\Parameter(name="page", in="query", description="Page number", @OA\Schema(type="integer", default=1)),
     *     @OA\Parameter(name="per_page", in="query", description="Items per page", @OA\Schema(type="integer", default=10)),
     *     @OA\Parameter(name="search", in="query", description="Search term for nama_pengeluaran, keterangan", @OA\Schema(type="string")),
     *     @OA\Parameter(name="sort_by", in="query", description="Sort field", @OA\Schema(type="string", enum={"id", "nama_pengeluaran", "jumlah", "bulan", "tahun", "tanggal_pengeluaran", "created_at"})),
     *     @OA\Parameter(name="sort_order", in="query", description="Sort order", @OA\Schema(type="string", enum={"asc", "desc"}, default="desc")),
     *     @OA\Parameter(name="bulan", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="tahun", in="query", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function index(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string|max:50',
        ]);
        $query = Pengeluaran::query();

        // Filter by bulan
        if ($request->has('bulan')) {
            $query->where('bulan', $request->get('bulan'));
        }

        // Filter by tahun
        if ($request->has('tahun')) {
            $query->where('tahun', $request->get('tahun'));
        }

        // Apply search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_pengeluaran', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%")
                  ->orWhere('jumlah', 'like', "%{$search}%")
                  ->orWhere('tahun', 'like', "%{$search}%");
            });
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        // Validate sort fields
        $allowedSortFields = ['id', 'nama_pengeluaran', 'jumlah', 'bulan', 'tahun', 'tanggal_pengeluaran', 'created_at'];
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'created_at';
        }
        
        $query->orderBy($sortBy, $sortOrder);

        // Apply pagination
        $perPage = $request->get('per_page', 10);
        $perPage = min(max($perPage, 1), 100); // Limit between 1 and 100

        return $query->paginate($perPage);
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
        $currentYear = date('Y');
        $validated = $request->validate([
            'nama_pengeluaran' => 'required|string|min:3|max:50',
            'jumlah' => 'required|numeric|min:1000|max:100000000',
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2000|max:2100',
            'tanggal_pengeluaran' => 'required|date',
            'keterangan' => 'nullable|string|max:200',
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
        $currentYear = date('Y');
        $validated = $request->validate([
            'nama_pengeluaran' => 'sometimes|required|string|min:3|max:50',
            'jumlah' => 'sometimes|required|numeric|min:1000|max:100000000',
            'bulan' => 'sometimes|required|integer|min:1|max:12',
            'tahun' => 'sometimes|required|integer|min:2000|max:2100',
            'tanggal_pengeluaran' => 'sometimes|required|date',
            'keterangan' => 'nullable|string|max:200',
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
