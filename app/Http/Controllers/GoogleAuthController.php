<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return Auth::user()->role === 'admin'
                ? redirect()->route('admin.dashboard')
                : redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function loginWithPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak terdaftar di sistem kami.'])->withInput();
        }

        if (empty($user->password)) {
            return back()->withErrors([
                'email' => 'Akun ini terdaftar via Google. Silakan masuk dengan Google lalu atur password di Dashboard.'
            ])->withInput();
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->boolean('remember'))) {
            $request->session()->regenerate();

            // JIKA ADMIN -> LANGSUNG MASUK KE /control-admin
            if (Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard')->with('success', 'Selamat datang di Control Center, ' . Auth::user()->name . '!');
            }

            return redirect()->route('dashboard')->with('success', 'Selamat datang kembali, ' . Auth::user()->name . '!');
        }

        return back()->withErrors(['email' => 'Email atau password salah.'])->withInput();
    }

    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')
                ->stateless()
                ->setHttpClient(new \GuzzleHttp\Client(['verify' => false]))
                ->user();

            $user = User::updateOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                ]
            );

            if (!$user->role) {
                $user->role = 'student';
                $user->save();
            }

            Auth::login($user, true);

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard')->with('success', 'Selamat datang Admin ' . $user->name . '!');
            }

            return redirect()->route('dashboard')->with('success', 'Berhasil masuk sebagai ' . $user->name . '!');
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Gagal masuk dengan Google: ' . $e->getMessage());
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('beranda');
    }
}
