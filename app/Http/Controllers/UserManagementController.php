<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    public static function middleware(): array
    {
        return [
            'auth',
            'super_admin',
        ];
    }

    /**
     * Display a listing of all users.
     */
    public function index()
    {
        $users = User::orderBy('name')->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'role' => 'required|in:super_admin,admin,user',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    /**
     * Generate password reset token for user.
     */
    public function generateResetToken(Request $request, User $user)
    {
        try {
            // Generate unique token
            $token = Str::random(60);

            // Log untuk debugging
            \Log::info('Generating reset token for user: ' . $user->email);

            // Store token in password reset table (you may need to create this table)
            // For now, return the token directly
            return response()->json([
                'success' => true,
                'token' => $token,
                'email' => $user->email,
                'name' => $user->name,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error generating reset token: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat link reset password: ' . $e->getMessage(),
            ], 500);
        }
    }
}
