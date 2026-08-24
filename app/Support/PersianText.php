<?php
// app/Support/PersianText.php

declare(strict_types=1);

namespace App\Support;

final class PersianText
{
    public const ZWNJ = "\u{200C}"; // نیم‌فاصله

    /** حروف مجاز فارسی (بدون رِنج آ-ی، چون آن رِنج ارقام عربی را هم مجاز می‌کند) */
    public const LETTERS = 'ءآاؤئبپتثجچحخدذرزژسشصضطظعغفقکگلمنوهی';

    /**
     * الگوی نام: کلمات فقط با «یک فاصله» یا «یک نیم‌فاصله» جدا می‌شوند.
     * ابتدا/انتها جداکننده ندارد و جداکننده تکراری هم مجاز نیست.
     */
    public const NAME_REGEX = '/^['.self::LETTERS.']+(?:[ '.self::ZWNJ.']['.self::LETTERS.']+)*$/u';

    /** یکسان‌سازی حروف عربی → فارسی */
    private const REPLACEMENTS = [
        "\u{064A}" => 'ی', // ي
        "\u{0649}" => 'ی', // ى
        "\u{06CD}" => 'ی', // ۍ
        "\u{0643}" => 'ک', // ك
        "\u{06AA}" => 'ک', // ػ
        "\u{0629}" => 'ه', // ة
        "\u{06C0}" => 'ه', // ۀ
        "\u{0623}" => 'ا', // أ
        "\u{0625}" => 'ا', // إ
        "\u{0671}" => 'ا', // ٱ
        "\u{0624}" => 'ؤ', // حفظ ؤ
    ];

    /** اعراب، کشیده، کاراکترهای جهت‌دهی و نامرئی */
    private const string REMOVE_REGEX = '/[\x{0640}\x{064B}-\x{065F}\x{0670}\x{06D6}-\x{06ED}'
    .'\x{200B}\x{200D}\x{200E}\x{200F}\x{202A}-\x{202E}\x{2066}-\x{2069}\x{FEFF}\x{00AD}]/u';

    public static function name(?string $value): string
    {
        $value = (string) $value;

        $value = strtr($value, self::REPLACEMENTS);
        $value = (string) preg_replace(self::REMOVE_REGEX, '', $value);

        // هر نوع فاصله (NBSP، تب، newline) → فاصله معمولی
        $value = (string) preg_replace('/[\p{Zs}\s]+/u', ' ', $value);

        // نیم‌فاصله تکراری → یکی
        $value = (string) preg_replace('/'.self::ZWNJ.'{2,}/u', self::ZWNJ, $value);

        // حذف فاصله قبل/بعد نیم‌فاصله
        $value = (string) preg_replace('/ *'.self::ZWNJ.' */u', self::ZWNJ, $value);

        // فاصله تکراری → یکی
        $value = (string) preg_replace('/ {2,}/', ' ', $value);

        // trim فاصله و نیم‌فاصله از دو سر
        return (string) preg_replace('/^[\s'.self::ZWNJ.']+|[\s'.self::ZWNJ.']+$/u', '', $value);
    }
}
