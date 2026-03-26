<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetUserTimezone
{
    // Valid IANA timezone strings — prevent injection of arbitrary values
    private const FALLBACK = 'Asia/Jakarta';

    public function handle(Request $request, Closure $next): Response
    {
        $timezone = $request->cookie('user_timezone');

        if ($timezone && $this->isValidTimezone($timezone)) {
            session(['user_timezone' => $timezone]);
        } elseif (!session('user_timezone')) {
            session(['user_timezone' => self::FALLBACK]);
        }

        return $next($request);
    }

    private function isValidTimezone(string $tz): bool
    {
        return in_array($tz, \DateTimeZone::listIdentifiers(), true);
    }
}
