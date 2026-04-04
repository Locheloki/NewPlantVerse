<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * AdminMiddleware
 * 
 * Verifies that the authenticated user has admin privileges before allowing access to protected routes.
 * Uses the User model's isAdmin() method which checks the is_admin database column.
 * 
 * Usage in routes:
 *   Route::middleware('admin')->group(function () { ... });
 */
class AdminMiddleware
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
        /**
         * Check if user is authenticated and has admin privileges.
         * If not, redirect to dashboard with an error message.
         */
        if (!$request->user() || !$request->user()->isAdmin()) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        return $next($request);
    }
}
