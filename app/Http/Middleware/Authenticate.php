<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    protected function redirectTo($request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        // simpan intended URL yang diminta user
        session(['url.intended' => $request->fullUrl()]);

        // minta layout untuk buka modal login
        session()->flash('open_auth_modal', 'login');

        // balik ke halaman sebelumnya
        return url()->previous() ?: url('/');
    }
}
