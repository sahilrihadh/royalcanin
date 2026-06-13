<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class AdminSessionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Set admin session configuration for admin routes
        Config::set('session.driver', config('session_admin.driver', 'file'));
        Config::set('session.cookie', config('session_admin.cookie', 'admin_session'));
        Config::set('session.files', config('session_admin.files', storage_path('framework/sessions/admin')));
        Config::set('session.path', config('session_admin.path', '/'));
        Config::set('session.domain', config('session_admin.domain', null));
        Config::set('session.secure', config('session_admin.secure', false));
        Config::set('session.http_only', config('session_admin.http_only', true));
        Config::set('session.same_site', config('session_admin.same_site', 'lax'));
        
        // Re-initialize the session with new config
        if ($request->hasSession()) {
            $request->session()->setName(config('session.cookie'));
        }
        
        return $next($request);
    }
}