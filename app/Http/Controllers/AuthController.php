<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        // If already logged in, redirect to dashboard or users
        if (Auth::check()) {
            return redirect('/dashboard');
        }
        
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginType = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $loginType => $request->username,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            if ($user->status !== 'نشط') {
                Auth::logout();
                return back()->withErrors([
                    'username' => 'هذا الحساب موقوف، يرجى مراجعة إدارة النظام.',
                ]);
            }

            // Optional: update last login time
            if (in_array('last_login_at', $user->getFillable())) {
                $user->update(['last_login_at' => now()]);
            }

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'username' => 'بيانات الدخول غير صحيحة.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login');
    }
}
