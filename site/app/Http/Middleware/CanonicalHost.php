<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CanonicalHost
{
    public function handle(Request $request, Closure $next): Response
    {
        if (strcasecmp($request->getHost(), 'www.keenguild.com') === 0) {
            $url = 'https://keenguild.com'.$request->getRequestUri();

            return redirect()->to($url, $request->isMethod('GET') || $request->isMethod('HEAD') ? 301 : 308);
        }

        return $next($request);
    }
}
