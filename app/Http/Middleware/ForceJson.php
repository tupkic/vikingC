<?php

namespace App\Http\Middleware;

use Closure;

class ForceJson
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
         /** tell user we will only accept JSON request **/
        $request->headers->set('Accept', 'application/json');

        $acceptHeader = $request->header('Accept');
        if ($acceptHeader != 'application/json') {
            return response(['message' => 'Only JSON requests are allowed'], 406);
        }
        return $next($request);
    }
}
