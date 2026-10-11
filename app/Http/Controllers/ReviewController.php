<?php

namespace App\Http\Controllers;

use App\Models\Mobil;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Mobil $mobil)
    {
        $data = $request->validate([
            'rating' => ['required','integer','min:1','max:5'],
            'name'   => ['nullable','string','max:80'],
            'comment'=> ['required','string','max:2000'],
            'agree'  => ['accepted'],
        ]);

        $userId = auth()->id();

        // ✅ Kalau login: cek sudah pernah review mobil ini belum
        if ($userId) {
            $exists = \App\Models\Review::where('mobil_id', $mobil->id)
                ->where('user_id', $userId)
                ->exists();

            if ($exists) {
                return back()->withErrors([
                    'review' => 'Kamu sudah memberikan review untuk mobil ini.',
                ])->withInput();
            }
        }

        \App\Models\Review::create([
            'mobil_id' => $mobil->id,
            'user_id'  => $userId,
            'name'     => $userId ? null : ($data['name'] ?: 'Anonim'),
            'rating'   => $data['rating'],
            'comment'  => $data['comment'],
        ]);

        return back()->with('success', 'Review berhasil dikirim!');
    }

}
