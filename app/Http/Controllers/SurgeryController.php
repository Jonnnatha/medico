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
            'starts_at' => 'required|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'doctor_id' => 'sometimes|exists:users,id',
        ]);

        if ($user->hasRole('doctor')) {
            $data['doctor_id'] = $user->id;
        }

        $surgery = Surgery::create($data);

        return response()->json($surgery, 201);
    }

    public function update(Request $request, Surgery $surgery)
    {
        $user = $request->user();
        abort_unless(
            $user->hasRole('admin') ||
                ($user->hasRole('doctor') && $surgery->doctor_id === $user->id),
            403
        );

        $data = $request->validate([
            'patient_name' => 'sometimes|string',
            'starts_at' => 'sometimes|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'doctor_id' => 'sometimes|exists:users,id',
            'status' => 'sometimes|string',
        ]);

        if ($user->hasRole('doctor')) {
            unset($data['doctor_id']);
        }

        $surgery->update($data);

        return response()->json($surgery);
    }

    public function destroy(Request $request, Surgery $surgery)
    {
        $user = $request->user();
        abort_unless(
            $user->hasRole('admin') ||
                ($user->hasRole('doctor') && $surgery->doctor_id === $user->id),
            403
        );

        $surgery->delete();

        return response()->noContent();
    }

    public function confirm(Request $request, Surgery $surgery)
    {
        $user = $request->user();
        abort_unless($user->hasRole('admin') || $user->hasRole('nurse'), 403);

        $surgery->update(['status' => Surgery::STATUS_CONFIRMED]);

        return response()->json($surgery);
    }

    public function cancel(Request $request, Surgery $surgery)
    {
        $user = $request->user();
        abort_unless($user->hasRole('admin') || $user->hasRole('nurse'), 403);

        $surgery->update(['status' => Surgery::STATUS_CANCELLED]);

        return response()->json($surgery);
    }
}
