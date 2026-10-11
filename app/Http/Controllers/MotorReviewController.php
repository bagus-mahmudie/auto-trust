<?php

namespace App\Http\Controllers;

use App\Models\Motor;
use Illuminate\Http\Request;

class MotorReviewController extends Controller
{
    public function store(Request $request, Motor $motor)
    {
        $data = $request->validate([
            'rating'  => ['required','integer','min:1','max:5'],
            'name'    => ['nullable','string','max:80'],
            'comment' => ['required','string','max:2000'],
            'agree'   => ['accepted'],
        ]);

        $userId = auth()->id();

        // ✅ Kalau login: cek sudah pernah review motor ini belum
        if ($userId) {
            $exists = \App\Models\MotorReview::where('motor_id', $motor->id)
                ->where('user_id', $userId)
                ->exists();

            if ($exists) {
                return back()->withErrors([
                    'review' => 'Kamu sudah memberikan review untuk motor ini.',
                ])->withInput();
            }
        }

        \App\Models\MotorReview::create([
            'motor_id' => $motor->id,
            'user_id'  => $userId,
            'name'     => $userId ? null : ($data['name'] ?: 'Anonim'),
            'rating'   => $data['rating'],
            'comment'  => $data['comment'],
        ]);

        return back()->with('success', 'Review berhasil dikirim!');
    }
}
