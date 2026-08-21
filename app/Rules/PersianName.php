<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final class PersianName implements ValidationRule
{
    // حروف فارسی + همزه‌ها + نیم‌فاصله + فاصله
    private const PATTERN = '/^[\x{0622}\x{0627}\x{0628}\x{067E}\x{062A}-\x{062C}\x{0686}'
        .'\x{062D}-\x{0632}\x{0698}\x{0633}-\x{063A}\x{0641}\x{0642}'
        .'\x{06A9}\x{06AF}\x{0644}-\x{0648}\x{06CC}\x{0621}\x{0623}'
        .'\x{0624}\x{0626}\x{0629}\x{0649}\x{200C}\x{0020}]+$/u';

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $value = self::clean((string) $value);

        if ($value === '') {
            $fail('این فیلد الزامی است.');
            return;
        }

        if (! preg_match(self::PATTERN, $value)) {
            $fail('فقط حروف فارسی مجاز است.');
            return;
        }

        if (mb_strlen($value) < 2) {
            $fail('حداقل ۲ حرف وارد کنید.');
        }
    }

    /** یکسان‌سازی نویسه‌های عربی و پاکسازی فاصله‌ها */
    public static function clean(?string $value): string
    {
        $v = (string) $value;

        $v = str_replace(
            ['ي', 'ك', 'ٱ', 'أ', 'إ', 'ة', 'ۀ', "\u{FEFF}", "\u{00A0}"],
            ['ی', 'ک', 'ا', 'ا', 'ا', 'ه', 'ه', '',        ' '],
            $v
        );

        // حذف اعراب (فتحه، کسره، تشدید…)
        $v = preg_replace('/[\x{064B}-\x{0652}\x{0670}]/u', '', $v);

        // فاصله‌های تکراری → یکی
        $v = preg_replace('/\s+/u', ' ', $v);

        // نیم‌فاصله‌ی چسبیده به فاصله را تمیز کن
        $v = preg_replace('/\s*\x{200C}\s*/u', "\u{200C}", $v);

        return trim($v, " \t\n\r\0\x0B\u{200C}");
    }
}
