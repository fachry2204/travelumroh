<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Agent;
use App\Models\Commission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AgentController extends Controller
{
    public function index(Request $request)
    {
        $agents = Agent::with(['user', 'representative.user'])
            ->when($request->search, fn($q) => $q->whereHas('user', fn($u) => $u->where('name', 'like', '%' . $request->search . '%')))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->withCount('bookings')
            ->withSum(['commissions' => fn($q) => $q->whereIn('status', ['approved', 'paid'])], 'commission_amount')
            ->orderByDesc('created_at')
            ->paginate($request->per_page ?? 15);

        return response()->json(['success' => true, 'data' => $agents]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'google_maps_url' => 'nullable|string',
            'representative_id' => 'nullable|exists:representatives,id',
            'commission_type' => 'required|in:percentage,fixed',
            'commission_value' => 'required|numeric|min:0',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'role_type' => 'agent',
            'status' => 'active',
        ]);
        $user->assignRole('agent');

        $agentCode = 'AG' . str_pad(Agent::max('id') + 1, 3, '0', STR_PAD_LEFT);
        $agent = Agent::create([
            'user_id' => $user->id,
            'representative_id' => $validated['representative_id'] ?? null,
            'agent_code' => $agentCode,
            'referral_code' => 'REF' . strtoupper(Str::random(5)),
            'address' => $validated['address'] ?? null,
            'google_maps_url' => $validated['google_maps_url'] ?? null,
            'commission_type' => $validated['commission_type'],
            'commission_value' => $validated['commission_value'],
            'status' => 'active',
        ]);

        return response()->json(['success' => true, 'message' => 'Agen berhasil ditambahkan', 'data' => $agent->load('user')], 201);
    }

    public function update(Request $request, Agent $agent)
    {
        $validated = $request->validate([
            'representative_id' => 'nullable|exists:representatives,id',
            'address' => 'nullable|string',
            'google_maps_url' => 'nullable|string',
            'commission_type' => 'sometimes|in:percentage,fixed',
            'commission_value' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|in:active,inactive',
        ]);

        $agent->update($validated);

        if ($request->has('name') || $request->has('phone')) {
            $agent->user->update(array_filter([
                'name' => $request->name,
                'phone' => $request->phone,
            ]));
        }

        return response()->json(['success' => true, 'message' => 'Agen berhasil diupdate', 'data' => $agent->load('user')]);
    }

    public function commissions(Request $request)
    {
        $commissions = Commission::with(['booking.package', 'agent.user'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->agent_id, fn($q) => $q->where('agent_id', $request->agent_id))
            ->orderByDesc('created_at')
            ->paginate($request->per_page ?? 15);

        return response()->json(['success' => true, 'data' => $commissions]);
    }

    public function payCommission(Request $request, Commission $commission)
    {
        $request->validate(['note' => 'nullable|string']);

        if ($commission->status !== 'approved') {
            return response()->json(['success' => false, 'message' => 'Komisi harus berstatus approved sebelum dibayarkan.'], 422);
        }

        $commission->update(['status' => 'paid', 'paid_at' => now(), 'note' => $request->note]);

        return response()->json(['success' => true, 'message' => 'Komisi berhasil ditandai sebagai sudah dibayar', 'data' => $commission]);
    }
}
