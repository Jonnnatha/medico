<?php

namespace App\Http\Controllers;

use App\Models\Surgery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SurgeryController extends Controller
{
    public function store(Request $request)
    {
        if (Auth::user()->hasRole('nurse')) {
            abort(403, 'Nurses cannot modify basic surgery data.');
        }

        $data = $request->validate([
            'room_id' => ['required', 'integer'],
            'starts_at' => ['required', 'date'],
            'duration_min' => ['required', 'integer', 'min:1'],
        ]);

        $surgery = Surgery::create($data);

        return response()->json($surgery, 201);
    }

    public function update(Request $request, Surgery $surgery)
    {
        if (Auth::user()->hasRole('nurse')) {
            abort(403, 'Nurses cannot modify basic surgery data.');
        }

        $data = $request->validate([
            'room_id' => ['sometimes', 'integer'],
            'starts_at' => ['sometimes', 'date'],
            'duration_min' => ['sometimes', 'integer', 'min:1'],
        ]);

        $surgery->update($data);

        return response()->json($surgery);
    }

    public function confirm(Surgery $surgery)
    {
        if (Auth::user()->hasRole('doctor')) {
            abort(403, 'Doctors cannot confirm surgeries.');
        }

        $surgery->update([
            'status' => 'confirmed',
            'confirmed_by' => Auth::id(),
            'canceled_by' => null,
        ]);

        return response()->json($surgery);
    }

    public function cancel(Surgery $surgery)
    {
        if (Auth::user()->hasRole('doctor')) {
            abort(403, 'Doctors cannot cancel surgeries.');
        }

        $surgery->update([
            'status' => 'canceled',
            'canceled_by' => Auth::id(),
            'confirmed_by' => null,
        ]);

        return response()->json($surgery);
    }
}
