<?php

namespace App\Http\Controllers;

use App\Models\Penghuni;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PenghuniController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/penghuni",
     *     tags={"Penghuni"},
     *     summary="Get list of penghuni",
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
     *         description="Search term for nama_lengkap or nomor_telepon",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Sort field",
     *         @OA\Schema(type="string", enum={"id", "nama_lengkap", "status_penghuni", "nomor_telepon", "status_menikah"})
     *     ),
     *     @OA\Parameter(
     *         name="sort_order",
     *         in="query",
     *         description="Sort order",
     *         @OA\Schema(type="string", enum={"asc", "desc"}, default="asc")
     *     ),
     *     @OA\Parameter(
     *         name="with_active_home",
     *         in="query",
     *         description="Filter penghuni with active home assignments",
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function index(Request $request)
    {
        $request->validate([
            'with_active_home' => 'nullable|in:true,false,0,1',
            'search' => 'nullable|string',
            'page' => 'nullable|integer',
            'per_page' => 'nullable|integer',
        ]);

        $query = Penghuni::query();

        if ($request->search) {
            $query->where('nama_lengkap', 'like', '%' . $request->search . '%');
        }

        if (in_array($request->with_active_home, ['true', '1'])) {
            $query->whereHas('rumahPenghuni', function ($query) {
                $query->whereNull('tanggal_keluar');
            });
        }

        $perPage = $request->per_page ?? 10;
        $penghuni = $query->paginate($perPage);

        return response()->json($penghuni);
    }

    /**
     * @OA\Post(
     *     path="/api/penghuni",
     *     tags={"Penghuni"},
     *     summary="Create new penghuni",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"nama_lengkap","status_penghuni","nomor_telepon","status_menikah"},
     *                 @OA\Property(property="nama_lengkap", type="string"),
     *                 @OA\Property(property="foto_ktp", type="file", format="binary"),
     *                 @OA\Property(property="status_penghuni", type="boolean"),
     *                 @OA\Property(property="nomor_telepon", type="string"),
     *                 @OA\Property(property="status_menikah", type="boolean")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Created")
     * )
     */
    public function store(Request $request)
    {
        \Log::info('Request all:', $request->all());
        \Log::info('Request files:', $request->file());

        $validated = $request->validate([
            'nama_lengkap' => 'required|string|min:3|max:50',
            'foto_ktp' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status_penghuni' => 'required|boolean',
            'nomor_telepon' => 'required|string|min:10|max:15',
            'status_menikah' => 'required|boolean',
        ]);

        // Handle file upload
        if ($request->hasFile('foto_ktp')) {
            $file = $request->file('foto_ktp');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('penghuni/ktp', $filename, 'public');
            $validated['foto_ktp'] = $filename; // Store only filename
        }

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
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="nama_lengkap", type="string"),
     *                 @OA\Property(property="foto_ktp", type="file", format="binary"),
     *                 @OA\Property(property="status_penghuni", type="boolean"),
     *                 @OA\Property(property="nomor_telepon", type="string"),
     *                 @OA\Property(property="status_menikah", type="boolean")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Updated")
     * )
     */
    public function update(Request $request, $id)
    {
        $penghuni = Penghuni::findOrFail($id);
        $validated = $request->validate([
            'nama_lengkap' => 'sometimes|required|string|min:3|max:50',
            'foto_ktp' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status_penghuni' => 'sometimes|required|boolean',
            'nomor_telepon' => 'sometimes|required|string|min:10|max:15',
            'status_menikah' => 'sometimes|required|boolean',
        ]);

        // Handle file upload
        if ($request->hasFile('foto_ktp')) {
            // Delete old file if exists
            if ($penghuni->foto_ktp) {
                $oldPath = str_replace('/storage/', 'public/', $penghuni->foto_ktp);
                if (Storage::exists($oldPath)) {
                    Storage::delete($oldPath);
                }
            }

            $file = $request->file('foto_ktp');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('penghuni/ktp', $filename, 'public');
            $validated['foto_ktp'] = $filename; // Store only filename
        }

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

        // Delete associated file if exists
        if ($penghuni->foto_ktp) {
            $path = str_replace('/storage/', 'public/', $penghuni->foto_ktp);
            if (Storage::exists($path)) {
                Storage::delete($path);
            }
        }

        $penghuni->delete();
        return response()->json(null, 204);
    }

    /**
     * Get a complete list of all penghuni for filtering purposes.
     *
     * @OA\Get(
     *     path="/api/penghuni/all",
     *     tags={"Penghuni"},
     *     summary="Get all penghuni (not paginated)",
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function all()
    {
        return Penghuni::all();
    }
}
