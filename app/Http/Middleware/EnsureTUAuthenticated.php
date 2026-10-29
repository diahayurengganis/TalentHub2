<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTUAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->session()->get('tu_authenticated') !== true) {
            return redirect()->route('tu.login');
        }

        return $next($request);
    }
}
