<?php

namespace App\Http\Controllers;

use App\Models\Mobil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MobilLikeController extends Controller
{
    public function toggle(Request $request, Mobil $mobil)
    {
        $userId = $request->user()->id;

        $exists = DB::table('mobil_likes')
            ->where('user_id', $userId)
            ->where('mobil_id', $mobil->id)
            ->exists();

        if ($exists) {
            DB::transaction(function () use ($userId, $mobil) {
                DB::table('mobil_likes')
                    ->where('user_id', $userId)
                    ->where('mobil_id', $mobil->id)
                    ->delete();

                DB::table('mobil')
                    ->where('id', $mobil->id)
                    ->update([
                        'likes_mobil' => DB::raw('GREATEST(COALESCE(likes_mobil,0) - 1, 0)')
                    ]);
            });

            $newCount = (int) DB::table('mobil')->where('id', $mobil->id)->value('likes_mobil');

            return response()->json(['liked' => false, 'likes' => $newCount]);
        }

        DB::transaction(function () use ($userId, $mobil) {
            DB::table('mobil_likes')->insert([
                'user_id' => $userId,
                'mobil_id' => $mobil->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('mobil')
                ->where('id', $mobil->id)
                ->update([
                    'likes_mobil' => DB::raw('COALESCE(likes_mobil,0) + 1')
                ]);
        });

        $newCount = (int) DB::table('mobil')->where('id', $mobil->id)->value('likes_mobil');

        return response()->json(['liked' => true, 'likes' => $newCount]);
    }
}
