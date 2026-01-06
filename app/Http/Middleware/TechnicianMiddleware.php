<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class TechnicianMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Get authenticated user
        $user = Auth::user();

        // Check if user is Super Admin (can access technician dashboard)
        if (!$user || $user->role !== User::ROLE_SUPER_ADMIN) {
            // If you want to allow regular admins with technician role,
            // you can add additional checks here
            abort(403, 'Unauthorized - Technician access required');
        }

        return $next($request);
    }
}
