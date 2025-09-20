<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfessorAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->session()->has('prof_id')) {
            return redirect()->route('prof.basic.login');
        }

        return $next($request);
    }
}

