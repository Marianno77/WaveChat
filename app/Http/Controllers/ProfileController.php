<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        return response()->json($request->user());
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $user = $request->user();
        if ($user->avatar && Storage::disk('public')->exists('avatars/' . $user->avatar)) {
            Storage::disk('public')->delete('avatars/' . $user->avatar);
        }

        $file = $request->file('avatar');
        $filename = uniqid() . '.' . $file->getClientOriginalExtension();
        $file->storeAs('public/avatars', $filename);
        $user->avatar = $filename;
        $user->save();

        return response()->json(['message' => 'Avatar zmieniony.']);
    }
}
