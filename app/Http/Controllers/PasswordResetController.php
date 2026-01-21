<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PasswordResetController extends Controller
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            // No middleware required for password reset (public access)
        ];
    }

    /**
     * Show the password reset form.
     */
    public function showResetForm($token)
    {
        // Check if token exists and is valid
        $resetData = Cache::get('password_reset_' . $token);

        if (!$resetData) {
            return view('auth.password-reset-error', [
                'error' => 'Link reset password tidak valid atau telah kadaluarsa.'
            ]);
        }

        return view('auth.password-reset', [
            'token' => $token,
            'email' => $resetData['email']
        ]);
    }

    /**
     * Handle the password reset request.
     */
    public function reset(Request $request, $token)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Check if token exists and is valid
        $resetData = Cache::get('password_reset_' . $token);

        if (!$resetData) {
            return back()->withErrors(['token' => 'Link reset password tidak valid atau telah kadaluarsa.']);
        }

        try {
            // Find user by email
            $user = User::where('email', $resetData['email'])->first();

            if (!$user) {
                return back()->withErrors(['email' => 'User tidak ditemukan.']);
            }

            // Update password
            $user->password = Hash::make($request->password);
            $user->save();

            // Delete the reset token
            Cache::forget('password_reset_' . $token);

            Log::info('Password reset successful for user: ' . $user->email);

            return redirect()->route('login')
                ->with('success', 'Password berhasil direset. Silakan login dengan password baru Anda.');
        } catch (\Exception $e) {
            Log::error('Error during password reset: ' . $e->getMessage());

            return back()->withErrors(['error' => 'Terjadi kesalahan saat mereset password. Silakan coba lagi.']);
        }
    }
}
