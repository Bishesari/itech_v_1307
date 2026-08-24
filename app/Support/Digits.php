<?php

declare(strict_types=1);

namespace App\Support;

final class Digits
{
    private const array PERSIAN = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];

    private const array ARABIC = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];

    private const array ENGLISH = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

    /** تبدیل ارقام فارسی و عربی به انگلیسی */
    public static function toEnglish(?string $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        return str_replace(
            [...self::PERSIAN, ...self::ARABIC],
            [...self::ENGLISH, ...self::ENGLISH],
            $value
        );
    }

    /** فقط ارقام انگلیسی — هر چیز دیگری حذف می‌شود */
    public static function only(?string $value): string
    {
        return preg_replace('/\D+/', '', self::toEnglish($value)) ?? '';
    }
}
