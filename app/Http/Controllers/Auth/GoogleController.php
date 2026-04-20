<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleGoogleCallback()
    {
        try {
            $user = Socialite::driver('google')->user();
            
            // Logika Pembatasan Domain @ugm.ac.id
            if (!Str::endsWith($user->email, '@ugm.ac.id')) {
                return redirect()->route('login')->withErrors([
                    'email' => 'Akses ditolak. Silakan gunakan email @ugm.ac.id untuk login.',
                ]);
            }

            $finduser = User::where('google_id', $user->id)
                            ->orWhere('email', $user->email)
                            ->first();

            if ($finduser) {
                // Update google_id jika user sudah terdaftar via email sebelumnya
                if (!$finduser->google_id) {
                    $finduser->update(['google_id' => $user->id]);
                }
                
                Auth::login($finduser);
                return redirect()->intended('dashboard');
            } else {
                $newUser = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'google_id' => $user->id,
                    'password' => null, // Password kosong karena login via OAuth
                ]);

                Auth::login($newUser);
                return redirect()->intended('dashboard');
            }

        } catch (Exception $e) {
            return redirect()->route('login')->withErrors([
                'email' => 'Terjadi kesalahan saat mencoba login dengan Google.',
            ]);
        }
    }
}
