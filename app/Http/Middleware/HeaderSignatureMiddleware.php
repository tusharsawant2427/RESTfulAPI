<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HeaderSignatureMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $header = 'X-Name')
    {
        $response = $next($request); // usually we call $next at the end thats before middleware and this is after middleware

        $response->headers->set($header, config('app.name'));

        return $response;
    }
}
