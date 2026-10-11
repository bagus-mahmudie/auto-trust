<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class ModalAuthController extends Controller
{
    // GET /login: tidak tampil halaman, hanya flash untuk buka modal
    public function loginGet(Request $request)
    {
        // simpan intended URL (biar setelah login balik ke halaman sebelumnya)
        session(['url.intended' => url()->previous() ?: url('/')]);

        return redirect()->to(url()->previous() ?: url('/'))
            ->with('open_auth_modal', 'login');
    }

    // POST /login: proses login
    public function loginPost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('open_auth_modal', 'login');
        }

        $remember = $request->boolean('remember');

        if (!Auth::attempt($validator->validated(), $remember)) {
            return back()
                ->withErrors(['email' => 'Email atau password salah.'])
                ->withInput()
                ->with('open_auth_modal', 'login');
        }

        $request->session()->regenerate();

        return redirect()->intended('/');
    }


    public function registerPost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('open_auth_modal', 'register');
        }

        $data = $validator->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        event(new Registered($user));

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended('/');
    }


    // POST /logout
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
