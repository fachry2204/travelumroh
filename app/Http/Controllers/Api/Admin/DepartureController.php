<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Departure;
use Illuminate\Http\Request;

class DepartureController extends Controller
{
    public function index()
    {
        $departures = Departure::with('package')->orderByDesc('created_at')->get();
        return response()->json(['success' => true, 'data' => $departures]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'package_id' => 'required|exists:packages,id',
            'group_code' => 'required|string|unique:departures',
            'departure_date' => 'required|date',
            'return_date' => 'required|date',
            'guide_name' => 'nullable|string',
            'airline' => 'nullable|string',
            'flight_number_departure' => 'nullable|string',
            'flight_number_return' => 'nullable|string',
            'status' => 'required|in:preparation,ready,departed,returned,cancelled',
        ]);

        $departure = Departure::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Grup keberangkatan berhasil ditambahkan',
            'data' => $departure
        ], 201);
    }

    public function show(Departure $departure)
    {
        return response()->json([
            'success' => true,
            'data' => $departure->load(['package', 'pilgrims'])
        ]);
    }

    public function update(Request $request, Departure $departure)
    {
        $validated = $request->validate([
            'package_id' => 'required|exists:packages,id',
            'group_code' => 'required|string|unique:departures,group_code,' . $departure->id,
            'departure_date' => 'required|date',
            'return_date' => 'required|date',
            'guide_name' => 'nullable|string',
            'airline' => 'nullable|string',
            'flight_number_departure' => 'nullable|string',
            'flight_number_return' => 'nullable|string',
            'status' => 'required|in:preparation,ready,departed,returned,cancelled',
        ]);

        $departure->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Grup keberangkatan berhasil diupdate',
            'data' => $departure
        ]);
    }

    public function destroy(Departure $departure)
    {
        $departure->delete();
        return response()->json([
            'success' => true,
            'message' => 'Grup keberangkatan berhasil dihapus'
        ]);
    }
}
