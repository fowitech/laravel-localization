<?php namespace Fowitech\Localization\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cookie;
use Fowitech\Localization\LanguageNegotiator;

class LocaleCookieRedirect extends LocalizationMiddlewareBase
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // If the URL of the request is in exceptions.
        if ($this->shouldIgnore($request)) {
            return $next($request);
        }

        $params = explode('/', $request->path());
        $locale = $request->cookie('locale', false);

        if (\count($params) > 0 && app('localization')->checkLocaleInSupportedLocales($params[0])) {
            return $next($request)->withCookie(cookie()->forever('locale', $params[0]));
        }

        if (empty($locale) && app('localization')->hideUrlAndAcceptHeader()){
            // When default locale is hidden and accept language header is true,
            // then compute browser language when no session has been set.
            // Once the session has been set, there is no need
            // to negotiate language from browser again.
            $negotiator = new LanguageNegotiator(
                app('localization')->getDefaultLocale(),
                app('localization')->getSupportedLocales(),
                $request
            );
            $locale = $negotiator->negotiateLanguage();
            Cookie::queue(Cookie::forever('locale', $locale));
        }

        if ($locale === false){
            $locale = app('localization')->getCurrentLocale();
        }

        if (
            $locale &&
            app('localization')->checkLocaleInSupportedLocales($locale) &&
            !(app('localization')->isHiddenDefault($locale))
        ) {
            $redirection = app('localization')->getLocalizedURL($locale);
            $redirectResponse = new RedirectResponse($redirection, 302, ['Vary' => 'Accept-Language']);

            return $redirectResponse->withCookie(cookie()->forever('locale', $params[0]));
        }

        return $next($request);
    }
}
