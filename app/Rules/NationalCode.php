<?php

declare(strict_types=1);

namespace App\Rules;

use App\Support\Digits;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final class NationalCode implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) && ! is_numeric($value)) {
            $fail('کد ملی نامعتبر است.');
            return;
        }

        if (! self::isValid((string) $value)) {
            $fail('کد ملی وارد شده معتبر نیست.');
        }
    }

    public static function isValid(string $code): bool
    {
        $code = Digits::only($code);

        // کدهای کمتر از ۱۰ رقم با صفر پر می‌شوند (مثلاً کد ملی تهران قدیم)
        if (strlen($code) < 8 || strlen($code) > 10) {
            return false;
        }
        $code = str_pad($code, 10, '0', STR_PAD_LEFT);

        // 1111111111 تا 9999999999 چک‌سام درست دارند ولی معتبر نیستند
        if (preg_match('/^(\d)\1{9}$/', $code)) {
            return false;
        }

        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += (int) $code[$i] * (10 - $i);
        }

        $remainder = $sum % 11;
        $control   = (int) $code[9];

        return $remainder < 2
            ? $control === $remainder
            : $control === 11 - $remainder;
    }

    /** خروجی استاندارد ۱۰ رقمی برای ذخیره */
    public static function normalize(string $code): string
    {
        return str_pad(Digits::only($code), 10, '0', STR_PAD_LEFT);
    }
}
