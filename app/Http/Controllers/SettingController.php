<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingController extends Controller
{
    public function edit()
    {
        $maxRooms = (int) Setting::getValue('max_rooms', 1);

        return Inertia::render('Settings/Edit', [
            'maxRooms' => $maxRooms,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'maxRooms' => ['required', 'integer', 'min:1'],
        ]);

        Setting::setValue('max_rooms', $data['maxRooms']);

        return redirect()->route('settings.edit');
    }
}
