<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final class NotIranianNationalCode implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $value = (string) $value;

        // فقط ارقام را جدا کن
        $digits = preg_replace('/\D/', '', $value);

        // اگر دقیقاً ۱۰ رقم بود و چک‌سام کد ملی را پاس کرد، رد کن
        if (strlen($digits) === 10 && NationalCode::isValid($digits)) {
            $fail('شناسه اختصاصی نمی‌تواند کد ملی باشد.');
        }
    }
}
