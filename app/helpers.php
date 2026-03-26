<?php

if (!function_exists('userTimezone')) {
    /**
     * Get the current user's timezone from their browser (stored in session).
     * Falls back to Asia/Jakarta (WIB) if not detected.
     */
    function userTimezone(): string
    {
        return session('user_timezone', 'Asia/Jakarta');
    }
}

if (!function_exists('userNow')) {
    /**
     * Get the current time in the user's local timezone.
     * Use this instead of now() when displaying time to the user.
     */
    function userNow(): \Illuminate\Support\Carbon
    {
        return \Illuminate\Support\Carbon::now(userTimezone());
    }
}

if (!function_exists('toUserTime')) {
    /**
     * Convert a UTC Carbon/datetime to the user's local timezone for display.
     */
    function toUserTime(mixed $datetime, string $format = 'd M Y, H:i'): string
    {
        if (!$datetime) return '-';
        return \Illuminate\Support\Carbon::parse($datetime)
            ->setTimezone(userTimezone())
            ->format($format);
    }
}
