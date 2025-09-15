<?php

namespace App\Http\Controllers;

use App\Models\Surgery;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;

class SurgeryController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $maxRooms = (int) Setting::getValue('max_rooms', 9);
        $query = Surgery::query();

        if ($user->hasRole(User::ROLE_DOCTOR)) {
            $query->where('doctor_id', $user->id);
        }

        $surgeries = $query->orderBy('starts_at')
            ->paginate($request->integer('per_page', 15));

        $surgeries->getCollection()->transform(function ($surgery) use ($maxRooms) {
            $surgery->is_conflict = $this->hasConflict($surgery, $maxRooms);
            return $surgery;
        });

        return response()->json($surgeries);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        abort_unless($user->hasRole(User::ROLE_ADMIN) || $user->hasRole(User::ROLE_DOCTOR), 403);

        $maxRooms = (int) Setting::getValue('max_rooms', 9);
        $data = $request->validate([
            'patient_name' => 'required|string',
            'starts_at' => 'required|date',
            'duration_min' => 'required|integer|min:1',
            'surgery_type' => 'required|string',
            'doctor_id' => 'sometimes|exists:users,id',
            'room' => 'required|integer|min:1|max:' . $maxRooms,
        ]);

        if ($user->hasRole(User::ROLE_DOCTOR)) {
            $data['doctor_id'] = $user->id;
        }

        $surgery = Surgery::create($data);
        $surgery->is_conflict = $this->hasConflict($surgery, $maxRooms);

        return response()->json($surgery, 201);
    }

    public function update(Request $request, Surgery $surgery)
    {
        $user = $request->user();
        abort_unless(
            $user->hasRole(User::ROLE_ADMIN) ||
                ($user->hasRole(User::ROLE_DOCTOR) && $surgery->doctor_id === $user->id),
            403
        );

        $maxRooms = (int) Setting::getValue('max_rooms', 9);
        $data = $request->validate([
            'patient_name' => 'sometimes|string',
            'starts_at' => 'sometimes|date',
            'duration_min' => 'sometimes|integer|min:1',
            'surgery_type' => 'sometimes|string',
            'doctor_id' => 'sometimes|exists:users,id',
            'status' => 'sometimes|string',
            'room' => 'required|integer|min:1|max:' . $maxRooms,
        ]);

        if ($user->hasRole(User::ROLE_DOCTOR)) {
            unset($data['doctor_id']);
        }

        $surgery->update($data);
        $surgery->is_conflict = $this->hasConflict($surgery, $maxRooms);

        return response()->json($surgery);
    }

    public function destroy(Request $request, Surgery $surgery)
    {
        $user = $request->user();
        abort_unless(
            $user->hasRole(User::ROLE_ADMIN) ||
                ($user->hasRole(User::ROLE_DOCTOR) && $surgery->doctor_id === $user->id),
            403
        );

        $surgery->delete();

        return response()->noContent();
    }

    public function confirm(Request $request, Surgery $surgery)
    {
        $user = $request->user();
        abort_unless($user->hasRole(User::ROLE_ADMIN) || $user->hasRole(User::ROLE_NURSE), 403);
        abort_if($surgery->status !== Surgery::STATUS_SCHEDULED, 400);

        $surgery->update([
            'status' => Surgery::STATUS_CONFIRMED,
            'confirmed_by' => $user->id,
            'canceled_by' => null,
        ]);

        $maxRooms = (int) Setting::getValue('max_rooms', 9);
        $surgery->is_conflict = $this->hasConflict($surgery, $maxRooms);

        return response()->json($surgery);
    }

    public function cancel(Request $request, Surgery $surgery)
    {
        $user = $request->user();
        abort_unless($user->hasRole(User::ROLE_ADMIN) || $user->hasRole(User::ROLE_NURSE), 403);

        abort_if($surgery->status === Surgery::STATUS_CANCELLED, 400);

        $surgery->update([
            'status' => Surgery::STATUS_CANCELLED,
            'canceled_by' => $user->id,
            'confirmed_by' => null,
        ]);

        $maxRooms = (int) Setting::getValue('max_rooms', 9);
        $surgery->is_conflict = $this->hasConflict($surgery, $maxRooms);

        return response()->json($surgery);
    }

    private function hasConflict(Surgery $surgery, int $maxRooms): bool
    {
        $overlap = Surgery::where('id', '!=', $surgery->id)
            ->where('starts_at', '<', $surgery->ends_at)
            ->where('ends_at', '>', $surgery->starts_at)
            ->count();

        return $overlap >= $maxRooms;
    }
}
