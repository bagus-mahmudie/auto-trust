<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileSettingsController extends Controller
{
    public function edit(Request $request)
    {
        // optional (biar view lebih rapi pakai $user langsung)
        $user = $request->user();

        return view('settings.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $mode = $request->input('mode', 'profile');

        // ===== MODE: PHOTO ONLY =====
        if ($mode === 'photo') {
            $validated = $request->validate([
                'foto_profile' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            ]);

            if ($request->hasFile('foto_profile')) {
                if ($user->foto_profile && Storage::disk('public')->exists($user->foto_profile)) {
                    Storage::disk('public')->delete($user->foto_profile);
                }

                $path = $request->file('foto_profile')->store('profile', 'public');
                $user->foto_profile = $path;
                $user->save();
            }

            return back()->with('success', 'Foto profil berhasil diperbarui.');
        }

        // ===== MODE: PROFILE DATA =====
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'name' => ['required', 'string', 'max:255'],

            // ✅ tambahan lokasi
            'lokasi' => ['nullable', 'string', 'max:150'],

            'deskripsi_user' => ['nullable', 'string', 'max:2000'],
        ]);

        $user->email = $validated['email'];
        $user->no_hp = $validated['no_hp'] ?? null;
        $user->name = $validated['name'];

        // ✅ simpan lokasi
        $user->lokasi = $validated['lokasi'] ?? null;

        $user->deskripsi_user = $validated['deskripsi_user'] ?? null;
        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
