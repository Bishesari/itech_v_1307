<?php

declare(strict_types=1);

namespace App\Rules;

use App\Support\Mobile;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final class IranianMobile implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) && ! is_numeric($value)) {
            $fail('validation.iranian_mobile')->translate();
            return;
        }
        if (! Mobile::isValid((string) $value)) {
            $fail('validation.iranian_mobile')->translate();
        }
    }
}
