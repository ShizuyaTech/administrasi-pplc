<?php
// Stubs for IDE auto-complete (Intelephense)
// These do NOT affect runtime - only help the IDE resolve types

// ─── Carbon ──────────────────────────────────────────────────────────────── //
namespace Carbon {
    /**
     * @method static static parse(mixed $time = null, \DateTimeZone|string|null $tz = null)
     * @method static static now(\DateTimeZone|string|null $tz = null)
     * @method static static create(int|null $year = 0, int|null $month = 1, int|null $day = 1, int|null $hour = 0, int|null $minute = 0, int|null $second = 0, \DateTimeZone|string|null $tz = null)
     * @method bool lt(\Carbon\Carbon|\DateTimeInterface $dt)
     * @method bool gt(\Carbon\Carbon|\DateTimeInterface $dt)
     * @method bool lte(\Carbon\Carbon|\DateTimeInterface $dt)
     * @method bool gte(\Carbon\Carbon|\DateTimeInterface $dt)
     * @method static addDay(int $value = 1)
     * @method static addDays(int $value = 1)
     * @method static subDay(int $value = 1)
     * @method static subDays(int $value = 1)
     * @method static addHour(int $value = 1)
     * @method static addMinute(int $value = 1)
     * @method float diffInMinutes(\Carbon\Carbon|\DateTimeInterface|null $dt = null, bool $absolute = true)
     * @method int diffInDays(\Carbon\Carbon|\DateTimeInterface|null $dt = null, bool $absolute = true)
     * @method string format(string $format)
     * @method string toDateString()
     * @method string toDateTimeString()
     */
    class Carbon extends \DateTime
    {
    }
}

// ─── Laravel Auth Guard ───────────────────────────────────────────────────── //
namespace Illuminate\Auth {
    class Guard
    {
        public function user(): ?\App\Models\User { throw new \RuntimeException('stub'); }
        public function id(): int|null { throw new \RuntimeException('stub'); }
        public function check(): bool { throw new \RuntimeException('stub'); }
        public function guest(): bool { throw new \RuntimeException('stub'); }
        public function login(\Illuminate\Contracts\Auth\Authenticatable $user, bool $remember = false): void { throw new \RuntimeException('stub'); }
        public function logout(): void { throw new \RuntimeException('stub'); }
        public function attempt(array $credentials = [], bool $remember = false): bool { throw new \RuntimeException('stub'); }
    }
}

// ─── DB Facade & auth() helper (global namespace) ────────────────────────── //
namespace {
    class DB
    {
        public static function transaction(\Closure $callback, int $attempts = 1): mixed { throw new \RuntimeException('stub'); }
        public static function beginTransaction(): void { throw new \RuntimeException('stub'); }
        public static function commit(): void { throw new \RuntimeException('stub'); }
        public static function rollBack(): void { throw new \RuntimeException('stub'); }
        public static function table(string $table): \Illuminate\Database\Query\Builder { throw new \RuntimeException('stub'); }
        public static function select(string $query, array $bindings = []): array { throw new \RuntimeException('stub'); }
        public static function insert(string $query, array $bindings = []): bool { throw new \RuntimeException('stub'); }
        public static function update(string $query, array $bindings = []): int { throw new \RuntimeException('stub'); }
        public static function delete(string $query, array $bindings = []): int { throw new \RuntimeException('stub'); }
        public static function statement(string $query, array $bindings = []): bool { throw new \RuntimeException('stub'); }
        public static function connection(string $name = null): \Illuminate\Database\Connection { throw new \RuntimeException('stub'); }
    }

    /**
     * @return \Illuminate\Auth\Guard
     */
    function auth(): \Illuminate\Auth\Guard { throw new \RuntimeException('stub'); }
}

