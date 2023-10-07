<?php 

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnforceJson
{
    /**
     * Enforce incoming requests to be JSON.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $request->headers->set('Accept', 'application/json');
        return $next($request);
    }
}
