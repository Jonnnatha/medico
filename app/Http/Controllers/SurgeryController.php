<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Surgery;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SurgeryController extends Controller
{
    public function create()
    {
        $maxRooms = (int) Setting::getValue('max_rooms', 1);

        return Inertia::render('Surgeries/Create', [
            'maxRooms' => $maxRooms,
        ]);
    }

    public function store(Request $request)
    {
        $maxRooms = (int) Setting::getValue('max_rooms', 1);

        $data = $request->validate([
            'room' => ['required', 'integer', 'min:1', 'max:' . $maxRooms],
        ]);

        Surgery::create($data);

        return redirect()->route('surgeries.create');
    }
}
