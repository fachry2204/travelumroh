<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Representative;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RepresentativeController extends Controller
{
    public function index(Request $request)
    {
        $representatives = Representative::with('user')
            ->withCount('agents')
            ->orderByDesc('created_at')
            ->paginate($request->per_page ?? 15);

        return response()->json(['success' => true, 'data' => $representatives]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:20',
            'region_name' => 'required|string|max:100',
            'office_address' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'role_type' => 'representative',
            'status' => 'active',
        ]);
        $user->assignRole('representative');

        $representative = Representative::create([
            'user_id' => $user->id,
            'region_name' => $validated['region_name'],
            'office_address' => $validated['office_address'] ?? null,
            'status' => 'active',
        ]);

        return response()->json(['success' => true, 'message' => 'Perwakilan berhasil ditambahkan', 'data' => $representative->load('user')], 201);
    }

    public function update(Request $request, Representative $representative)
    {
        $validated = $request->validate([
            'region_name' => 'sometimes|string|max:100',
            'office_address' => 'nullable|string',
            'status' => 'sometimes|in:active,inactive',
        ]);

        $representative->update($validated);

        if ($request->has('name') || $request->has('phone')) {
            $representative->user->update(array_filter([
                'name' => $request->name,
                'phone' => $request->phone,
            ]));
        }

        return response()->json(['success' => true, 'message' => 'Perwakilan berhasil diupdate', 'data' => $representative->load('user')]);
    }

    public function destroy(Representative $representative)
    {
        if ($representative->user) {
            $representative->user->delete();
        } else {
            $representative->delete();
        }
        return response()->json(['success' => true, 'message' => 'Perwakilan berhasil dihapus']);
    }
}
