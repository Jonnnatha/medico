<?php

namespace App\Http\Controllers;

use App\Models\Surgery;
use Illuminate\Http\Request;

class SurgeryController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Surgery::query();

        if ($user->hasRole('doctor')) {
            $query->where('doctor_id', $user->id);
        }

        $surgeries = $query->orderBy('starts_at')
            ->paginate($request->integer('per_page', 15));

        return response()->json($surgeries);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        abort_unless($user->hasRole('admin') || $user->hasRole('doctor'), 403);

        $data = $request->validate([
            'patient_name' => 'required|string',
            'room' => 'required|integer',
            'starts_at' => 'required|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'doctor_id' => 'sometimes|exists:users,id',
        ]);

        if ($user->hasRole('doctor')) {
            $data['doctor_id'] = $user->id;
        }

        $data['created_by'] = $user->id;
        $start = $data['starts_at'];
        $end = $data['ends_at'] ?? $start;

        $conflict = Surgery::where('room', $data['room'])
            ->where(function ($query) use ($start, $end) {
                $query->where('starts_at', '<', $end)
                    ->where('ends_at', '>', $start);
            })->exists();

        $data['is_conflict'] = $conflict;

        $surgery = Surgery::create($data);

        return response()->json($surgery->refresh(), 201);
    }

    public function update(Request $request, Surgery $surgery)
    {
        $user = $request->user();
        abort_unless($user->hasRole('admin') || $surgery->created_by === $user->id, 403);

        $data = $request->validate([
            'patient_name' => 'sometimes|string',
            'room' => 'sometimes|integer',
            'starts_at' => 'sometimes|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'status' => 'sometimes|string',
        ]);

        $room = $data['room'] ?? $surgery->room;
        $start = $data['starts_at'] ?? $surgery->starts_at;
        $end = $data['ends_at'] ?? $surgery->ends_at;

        $conflict = Surgery::where('room', $room)
            ->where('id', '!=', $surgery->id)
            ->where(function ($query) use ($start, $end) {
                $query->where('starts_at', '<', $end)
                    ->where('ends_at', '>', $start);
            })->exists();

        $data['is_conflict'] = $conflict;

        $surgery->update($data);

        return response()->json($surgery->refresh());
    }

    public function destroy(Request $request, Surgery $surgery)
    {
        $user = $request->user();
        abort_unless($user->hasRole('admin') || $surgery->created_by === $user->id, 403);

        $surgery->delete();

        return response()->noContent();
    }

    public function confirm(Request $request, Surgery $surgery)
    {
        $user = $request->user();
        abort_unless($user->hasRole('admin') || $user->hasRole('nurse'), 403);
        abort_if($user->hasRole('doctor') && !$user->hasRole('admin'), 403);

        $surgery->update([
            'status' => Surgery::STATUS_CONFIRMED,
            'confirmed_by' => $user->id,
        ]);

        return response()->json($surgery->refresh());
    }

    public function cancel(Request $request, Surgery $surgery)
    {
        $user = $request->user();
        abort_unless($user->hasRole('admin') || $user->hasRole('nurse'), 403);
        abort_if($user->hasRole('doctor') && !$user->hasRole('admin'), 403);

        $surgery->update([
            'status' => Surgery::STATUS_CANCELLED,
            'canceled_by' => $user->id,
        ]);

        return response()->json($surgery->refresh());
    }
}
