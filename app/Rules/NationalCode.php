<?php

declare(strict_types=1);

namespace App\Rules;

use App\Support\Digits;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final readonly class NationalCode implements ValidationRule
{
    /**
     * @param  bool  $strict  اگر true باشد فقط کد ۱۰ رقمی دقیق پذیرفته می‌شود
     */
    public function __construct(
        private bool $strict = false
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) && ! is_numeric($value)) {
            $fail('validation.national_code')->translate();

            return;
        }

        if (! self::isValid((string) $value, $this->strict)) {
            $fail('validation.national_code')->translate();
        }
    }

    public static function isValid(string $code, bool $strict = false): bool
    {
        $code = Digits::only($code);

        if ($strict && strlen($code) !== 10) {
            return false;
        }

        // کدهای کمتر از ۱۰ رقم با صفر پر می‌شوند (مثلاً کدهای قدیمی تهران)
        if (strlen($code) < 8 || strlen($code) > 10) {
            return false;
        }

        $code = str_pad($code, 10, '0', STR_PAD_LEFT);

        // کدهای تکراری مثل 1111111111
        if (preg_match('/^(\d)\1{9}$/', $code)) {
            return false;
        }

        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += (int) $code[$i] * (10 - $i);
        }

        $remainder = $sum % 11;
        $control = (int) $code[9];

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
