<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingController extends Controller
{
    public function edit(Request $request)
    {
        abort_unless($request->user()?->isAdmin(), 403);

        return Inertia::render('Settings/Index', [
            'maxRooms' => (int) Setting::getValue('max_rooms', 9),
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        abort_unless($user?->isAdmin(), 403);

        $data = $request->validate([
            'max_rooms' => 'required|integer|min:1',
        ]);

        Setting::setValue('max_rooms', (string) $data['max_rooms']);

        return redirect()->back();
    }
}
