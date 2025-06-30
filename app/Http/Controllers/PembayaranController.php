<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PembayaranController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/pembayaran",
     *     tags={"Pembayaran"},
     *     summary="Get list of pembayaran",
     *     @OA\Parameter(name="page", in="query", description="Page number", @OA\Schema(type="integer", default=1)),
     *     @OA\Parameter(name="per_page", in="query", description="Items per page", @OA\Schema(type="integer", default=10)),
     *     @OA\Parameter(name="search", in="query", description="Search term for penghuni name, rumah number, jenis_iuran", @OA\Schema(type="string")),
     *     @OA\Parameter(name="sort_by", in="query", description="Sort field", @OA\Schema(type="string", enum={"id", "bulan", "tahun", "jenis_iuran", "status", "tanggal_bayar"})),
     *     @OA\Parameter(name="sort_order", in="query", description="Sort order", @OA\Schema(type="string", enum={"asc", "desc"}, default="desc")),
     *     @OA\Parameter(name="rumah_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="penghuni_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="bulan", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="tahun", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="status", in="query", @OA\Schema(type="boolean")),
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function index(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string|max:50',
        ]);
        $query = Pembayaran::with(['rumah', 'penghuni']);

        // Filter by rumah_id
        if ($request->has('rumah_id')) {
            $query->where('rumah_id', $request->get('rumah_id'));
        }

        // Filter by penghuni_id
        if ($request->has('penghuni_id')) {
            $query->where('penghuni_id', $request->get('penghuni_id'));
        }

        // Filter by bulan
        if ($request->has('bulan')) {
            $query->where('bulan', $request->get('bulan'));
        }

        // Filter by tahun
        if ($request->has('tahun')) {
            $query->where('tahun', $request->get('tahun'));
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        // Apply search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('penghuni', function ($subQ) use ($search) {
                    $subQ->where('nama_lengkap', 'like', "%{$search}%");
                })
                ->orWhereHas('rumah', function ($subQ) use ($search) {
                    $subQ->where('nomor_rumah', 'like', "%{$search}%");
                })
                ->orWhere('jenis_iuran', 'like', "%{$search}%");
            });
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        // Validate sort fields
        $allowedSortFields = ['id', 'bulan', 'tahun', 'jenis_iuran', 'status', 'tanggal_bayar', 'created_at'];
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
            'tahun' => 'required|integer|min:2000|max:2100',
            'jenis_iuran' => 'required|in:satpam,kebersihan',
            'status' => 'required|boolean',
            'tanggal_bayar' => 'nullable|date',
        ]);
        // Set jumlah if not provided
        if (!isset($validated['jumlah']) || $validated['jumlah'] === null) {
            if ($validated['jenis_iuran'] === 'satpam') {
                $validated['jumlah'] = 100000;
            } elseif ($validated['jenis_iuran'] === 'kebersihan') {
                $validated['jumlah'] = 15000;
            }
        }
        $pembayaran = Pembayaran::create($validated);
        $pembayaran->load(['rumah', 'penghuni']);
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
        $pembayaran = Pembayaran::with(['rumah', 'penghuni'])->findOrFail($id);
        return $pembayaran;
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
        try {
        $pembayaran = Pembayaran::findOrFail($id);
            $input = $request->all();
            // Accept status as 0/1 or true/false, cast to boolean for validation
            if (isset($input['status'])) {
                if ($input['status'] === 1 || $input['status'] === '1' || $input['status'] === true || $input['status'] === 'true') {
                    $input['status'] = true;
                } else {
                    $input['status'] = false;
                }
            }
            $validated = validator($input, [
            'rumah_id' => 'sometimes|required|integer|exists:rumah,id',
            'penghuni_id' => 'sometimes|required|integer|exists:penghuni,id',
            'bulan' => 'sometimes|required|integer|min:1|max:12',
            'tahun' => 'sometimes|required|integer|min:2000|max:2100',
            'jenis_iuran' => 'sometimes|required|in:satpam,kebersihan',
                'jumlah' => 'nullable|numeric',
            'status' => 'sometimes|required|boolean',
            'tanggal_bayar' => 'nullable|date',
            ])->validate();
            // Set jumlah if not provided
            if (!isset($validated['jumlah']) || $validated['jumlah'] === null) {
                if (isset($validated['jenis_iuran'])) {
                    if ($validated['jenis_iuran'] === 'satpam') {
                        $validated['jumlah'] = 100000;
                    } elseif ($validated['jenis_iuran'] === 'kebersihan') {
                        $validated['jumlah'] = 15000;
                    }
                } else {
                    // fallback to existing jenis_iuran if not in request
                    if ($pembayaran->jenis_iuran === 'satpam') {
                        $validated['jumlah'] = 100000;
                    } elseif ($pembayaran->jenis_iuran === 'kebersihan') {
                        $validated['jumlah'] = 15000;
                    }
                }
            }
        $pembayaran->update($validated);
            $pembayaran->load(['rumah', 'penghuni']);
        return response()->json($pembayaran);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update pembayaran', 'details' => $e->getMessage()], 500);
        }
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
