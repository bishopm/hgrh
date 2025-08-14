<?php

namespace Bishopm\Hgrh\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

class AdminRoute
{
    public function handle($request, Closure $next)
    {
        if (!$this->isAdmin($request)) {
            abort(Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }

    protected function isAdmin($request)
    {
        return true;
        //return $request->user()->hasRole('Super Admin');
    }
}