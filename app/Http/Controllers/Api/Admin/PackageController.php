<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\PackageItinerary;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        $packages = Package::withCount('bookings')
            ->when($request->search, fn($q) => $q->where('name', 'like', '%' . $request->search . '%'))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->orderByDesc('created_at')
            ->paginate($request->per_page ?? 15);

        return response()->json(['success' => true, 'data' => $packages]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:packages',
            'name' => 'required|string|max:200',
            'departure_date' => 'required|date',
            'return_date' => 'required|date|after:departure_date',
            'duration_days' => 'required|integer|min:1',
            'airline' => 'nullable|string|max:100',
            'departure_airport' => 'nullable|string|max:100',
            'makkah_hotel' => 'nullable|string|max:200',
            'madinah_hotel' => 'nullable|string|max:200',
            'price_quad' => 'nullable|numeric|min:0',
            'price_triple' => 'nullable|numeric|min:0',
            'price_double' => 'nullable|numeric|min:0',
            'minimum_dp' => 'nullable|numeric|min:0',
            'quota' => 'required|integer|min:1',
            'facilities' => 'nullable|string',
            'excluded' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'in:active,inactive',
            'itineraries' => 'nullable|array',
            'itineraries.*.day_number' => 'required|integer',
            'itineraries.*.title' => 'required|string',
            'itineraries.*.description' => 'nullable|string',
        ]);

        $validated['slug'] = Str::slug($validated['name'] . '-' . $validated['code']);
        $validated['remaining_seat'] = $validated['quota'];

        $itineraries = $validated['itineraries'] ?? [];
        unset($validated['itineraries']);

        $package = Package::create($validated);

        foreach ($itineraries as $itinerary) {
            $package->itineraries()->create($itinerary);
        }

        return response()->json(['success' => true, 'message' => 'Paket berhasil dibuat', 'data' => $package->load('itineraries')], 201);
    }

    public function show(Package $package)
    {
        return response()->json(['success' => true, 'data' => $package->load(['itineraries', 'bookings'])]);
    }

    public function update(Request $request, Package $package)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:200',
            'departure_date' => 'sometimes|date',
            'return_date' => 'sometimes|date',
            'duration_days' => 'sometimes|integer|min:1',
            'airline' => 'nullable|string|max:100',
            'departure_airport' => 'nullable|string|max:100',
            'makkah_hotel' => 'nullable|string|max:200',
            'madinah_hotel' => 'nullable|string|max:200',
            'price_quad' => 'nullable|numeric|min:0',
            'price_triple' => 'nullable|numeric|min:0',
            'price_double' => 'nullable|numeric|min:0',
            'minimum_dp' => 'nullable|numeric|min:0',
            'quota' => 'sometimes|integer|min:1',
            'remaining_seat' => 'sometimes|integer|min:0',
            'facilities' => 'nullable|string',
            'excluded' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'in:active,inactive',
            'itineraries' => 'nullable|array',
            'itineraries.*.day_number' => 'required|integer',
            'itineraries.*.title' => 'required|string',
            'itineraries.*.description' => 'nullable|string',
        ]);

        $itineraries = $validated['itineraries'] ?? null;
        unset($validated['itineraries']);

        $package->update($validated);

        if ($itineraries !== null) {
            $package->itineraries()->delete();
            foreach ($itineraries as $itinerary) {
                $package->itineraries()->create($itinerary);
            }
        }

        return response()->json(['success' => true, 'message' => 'Paket berhasil diupdate', 'data' => $package->load('itineraries')]);
    }

    public function destroy(Package $package)
    {
        if ($package->bookings()->exists()) {
            return response()->json(['success' => false, 'message' => 'Paket tidak dapat dihapus karena sudah ada booking.'], 422);
        }
        $package->delete();
        return response()->json(['success' => true, 'message' => 'Paket berhasil dihapus']);
    }

    public function uploadImage(Request $request, Package $package)
    {
        $request->validate(['image' => 'required|image|max:2048']);
        $path = $request->file('image')->store('packages', 'public');
        $package->update(['featured_image' => $path]);
        return response()->json(['success' => true, 'data' => ['path' => $path]]);
    }
}
