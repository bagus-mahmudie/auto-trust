<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        /**
         * Global data untuk semua card mobil:
         * daftar mobil_id yang sudah di-like user login.
         *
         * Efek: heart akan selalu sinkron di Home, Rekomendasi, Populer,
         * Seller profile, section rekomendasi di detail, dll.
         *
         * Ringan: 1 query per request (hanya saat user login).
         */
        View::composer('*', function ($view) {
            if (!Auth::check()) {
                $view->with('likedMobilIds', []);
                $view->with('likedMotorIds', []);
                return;
            }

            $likedMobilIds = DB::table('mobil_likes')
                ->where('user_id', Auth::id())
                ->pluck('mobil_id')
                ->all();
            
            $likedMotorIds = DB::table('motor_likes')
                ->where('user_id', Auth::id())
                ->pluck('motor_id')
                ->all();

            $view->with('likedMobilIds', $likedMobilIds);
            $view->with('likedMotorIds', $likedMotorIds);
        });
    }
}
