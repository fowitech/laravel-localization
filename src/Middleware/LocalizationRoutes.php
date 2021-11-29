<?php

namespace Fowitech\Localization\Middleware;

use Closure;

class LocalizationRoutes extends LocalizationMiddlewareBase
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // If the URL of the request is in exceptions.
        if ($this->shouldIgnore($request)) {
            return $next($request);
        }

        $app = app();

        $routeName = $app['localization']->getRouteNameFromAPath($request->getUri());

        $app['localization']->setRouteName($routeName);

        return $next($request);
    }
}
