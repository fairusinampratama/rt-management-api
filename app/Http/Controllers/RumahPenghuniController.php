<?php

namespace App\Http\Controllers;

use App\Models\RumahPenghuni;
use Illuminate\Http\Request;

class RumahPenghuniController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return RumahPenghuni::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rumah_id' => 'required|integer|exists:rumah,id',
            'penghuni_id' => 'required|integer|exists:penghuni,id',
            'tanggal_masuk' => 'required|date',
            'tanggal_keluar' => 'nullable|date',
        ]);
        $rumahPenghuni = RumahPenghuni::create($validated);
        return response()->json($rumahPenghuni, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return RumahPenghuni::findOrFail($id);
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
            'tanggal_masuk' => 'sometimes|required|date',
            'tanggal_keluar' => 'nullable|date',
        ]);
        $rumahPenghuni->update($validated);
        return response()->json($rumahPenghuni);
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
