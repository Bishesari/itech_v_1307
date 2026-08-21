<?php

declare(strict_types=1);

namespace App\Support;

final class Digits
{
    private const array PERSIAN = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];

    private const array ARABIC = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];

    private const array LATIN = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

    /** ۱۲۳ و ١٢٣ → 123 */
    public static function toLatin(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return str_replace(
            [...self::PERSIAN, ...self::ARABIC],
            [...self::LATIN,   ...self::LATIN],
            $value
        );
    }

    /** حذف هر چیزی جز رقم — برای کد ملی و موبایل */
    public static function only(?string $value): string
    {
        return preg_replace('/\D/', '', (string) self::toLatin($value)) ?? '';
    }
}
