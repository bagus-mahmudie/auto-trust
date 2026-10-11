<?php

namespace App\Http\Controllers;

use App\Models\Motor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MotorLikeController extends Controller
{
    public function toggle(Request $request, Motor $motor)
    {
        $userId = $request->user()->id;

        $exists = DB::table('motor_likes')
            ->where('user_id', $userId)
            ->where('motor_id', $motor->id)
            ->exists();

        if ($exists) {
            DB::transaction(function () use ($userId, $motor) {
                DB::table('motor_likes')
                    ->where('user_id', $userId)
                    ->where('motor_id', $motor->id)
                    ->delete();

                DB::table('motor')
                    ->where('id', $motor->id)
                    ->update([
                        'likes_motor' => DB::raw('GREATEST(COALESCE(likes_motor,0) - 1, 0)')
                    ]);
            });

            $newCount = (int) DB::table('motor')->where('id', $motor->id)->value('likes_motor');
            return response()->json(['liked' => false, 'likes' => $newCount]);
        }

        DB::transaction(function () use ($userId, $motor) {
            DB::table('motor_likes')->insert([
                'user_id' => $userId,
                'motor_id' => $motor->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('motor')
                ->where('id', $motor->id)
                ->update([
                    'likes_motor' => DB::raw('COALESCE(likes_motor,0) + 1')
                ]);
        });

        $newCount = (int) DB::table('motor')->where('id', $motor->id)->value('likes_motor');
        return response()->json(['liked' => true, 'likes' => $newCount]);
    }
}
