<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserIsSubscribed
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        // This user is not a paying customer...
        if ($request->user() && (!$request->user()->subscribed('annually') && !$request->user()->subscribed('monthly'))) {
            return redirect('dashboard');
        }

        return $next($request);
    }
}
