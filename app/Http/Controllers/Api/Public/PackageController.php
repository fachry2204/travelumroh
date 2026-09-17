<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        $query = Package::with('itineraries')->where('status', 'active');

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->month) {
            $query->whereMonth('departure_date', $request->month);
        }

        if ($request->year) {
            $query->whereYear('departure_date', $request->year);
        }

        if ($request->min_price) {
            $query->where(function($q) use ($request) {
                $q->where('price_quad', '>=', $request->min_price)
                  ->orWhere('price_triple', '>=', $request->min_price)
                  ->orWhere('price_double', '>=', $request->min_price);
            });
        }

        $packages = $query->orderBy('departure_date')->paginate($request->per_page ?? 9);

        return response()->json([
            'success' => true,
            'data' => $packages,
        ]);
    }

    public function show($slug)
    {
        $package = Package::with(['itineraries'])
            ->where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $package,
        ]);
    }
}
