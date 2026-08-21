<?php

declare(strict_types=1);

namespace App\Rules;

use App\Support\Digits;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final class IranMobile implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (self::normalize((string) $value) === null) {
            $fail('شماره موبایل معتبر نیست.');
        }
    }

    /**
     * همه‌ی این‌ها → 09123456789
     *   +989123456789 | 00989123456789 | 989123456789
     *   9123456789    | 0912 345 6789  | ۰۹۱۲۳۴۵۶۷۸۹
     */
    public static function normalize(?string $value): ?string
    {
        $d = Digits::only($value);

        if ($d === '') {
            return null;
        }

        $d = match (true) {
            str_starts_with($d, '0098') => substr($d, 4),
            str_starts_with($d, '98') => substr($d, 2),
            str_starts_with($d, '0') => substr($d, 1),
            default => $d,
        };

        // اکنون باید 9xxxxxxxxx باشد
        if (! preg_match('/^9\d{9}$/', $d)) {
            return null;
        }

        return '0'.$d;
    }
}
