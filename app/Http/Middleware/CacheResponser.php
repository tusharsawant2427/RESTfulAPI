<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CacheResponser
{
    /**
     * Handle an incoming request.
     *
     * @param    \Illuminate\Http\Request  $request
     * @param    \Closure  $next
     * @return  mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $url = request()->url();
        $queryParams = request()->query();

        ksort($queryParams);

        $queryString = http_build_query($queryParams);

        $fullUrl = "{$url}?{$queryString}";
        if(Cache::has($fullUrl)) {
            return Cache::get($fullUrl);
        }
        return $next($request);
    }
}
