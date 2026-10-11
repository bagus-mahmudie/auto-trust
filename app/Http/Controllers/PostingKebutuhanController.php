<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KebutuhanPosting;

class PostingKebutuhanController extends Controller
{
    public function create()
    {
        $brands = ['Honda', 'Toyota', 'Daihatsu', 'Suzuki', 'Mitsubishi', 'Nissan', 'Wuling'];
        $bodyTypes = ['SUV', 'MPV', 'Sedan', 'Hatchback', 'Pickup', 'Truck', 'Van'];

        $user = auth()->user();

        // SESUAIKAN: kalau kolom lokasi user kamu beda, ganti prioritasnya di sini
        $kotaUser = $user->kota ?? $user->lokasi ?? $user->alamat ?? '';

        return view('posting.kebutuhan', compact('brands', 'bodyTypes', 'user', 'kotaUser'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul'       => ['required', 'string', 'max:160'],
            'merek'       => ['required', 'string', 'max:80'],
            'tipe_bodi'   => ['required', 'string', 'max:80'],
            'transmisi'   => ['required', 'string', 'max:80'],
            'range_harga' => ['required', 'string', 'max:80'],
        ]);

        $user = auth()->user();
        $kotaUser = $user->kota ?? $user->lokasi ?? $user->alamat ?? '';

        KebutuhanPosting::create([
            'user_id'     => $user->id,
            'nama'        => $user->name,
            'kota'        => $kotaUser,
            'judul'       => $validated['judul'],
            'merek'       => $validated['merek'] ?? null,
            'tipe_bodi'   => $validated['tipe_bodi'] ?? null,
            'transmisi'   => $validated['transmisi'] ?? null,
            'range_harga' => $validated['range_harga'] ?? null,
        ]);

        return redirect()->route('settings.edit')->with('success', 'Posting kebutuhan berhasil dibuat.');
    }
}
