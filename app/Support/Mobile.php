<?php

declare(strict_types=1);

namespace App\Support;

final class Mobile
{
    /**
     * تبدیل هر فرمتی به 09xxxxxxxxx
     * اگر شماره قابل تشخیص نبود، همان ارقام خام برمی‌گردد
     * (تا کاربر ورودی خودش را در فرم ببیند و خطا بگیرد)
     */
    public static function normalize(?string $value): string
    {
        $digits = Digits::only($value);

        if ($digits === '') {
            return '';
        }

        if (preg_match('/^(?:00|0)?(?:98)?(9\d{9})$/', $digits, $matches) === 1) {
            return '0'.$matches[1];
        }

        return $digits;
    }

    /** آیا شماره یک موبایل معتبر ایرانی است؟ */
    public static function isValid(?string $value): bool
    {
        return preg_match('/^09\d{9}$/', self::normalize($value)) === 1;
    }
}
