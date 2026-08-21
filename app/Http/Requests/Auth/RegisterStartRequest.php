<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Rules\IranMobile;
use App\Rules\NationalCode;
use App\Rules\PersianName;
use Illuminate\Foundation\Http\FormRequest;

class RegisterStartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** ← نرمال‌سازی قبل از اعتبارسنجی. قلب ماجرا همین‌جاست. */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'first_name' => PersianName::clean($this->input('first_name')),
            'last_name' => PersianName::clean($this->input('last_name')),
            'national_code' => NationalCode::normalize((string) $this->input('national_code')),
            'mobile' => IranMobile::normalize($this->input('mobile'))
                ?? $this->input('mobile'),   // نامعتبر بماند تا rule پیام بدهد
        ]);
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:60', new PersianName],
            'last_name' => ['required', 'string', 'max:60', new PersianName],
            'national_code' => ['required', 'string', 'size:10', new NationalCode],
            'mobile' => ['required', 'string', 'size:11', new IranMobile],
        ];
    }

    public function attributes(): array
    {
        return [
            'first_name' => 'نام',
            'last_name' => 'نام خانوادگی',
            'national_code' => 'کد ملی',
            'mobile' => 'شماره موبایل',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute الزامی است.',
            'max' => ':attribute نباید بیشتر از :max حرف باشد.',
        ];
    }

    /** داده‌ی تمیز و آماده برای لایه‌ی بعد
     * @param  null  $key
     * @param  null  $default
     */
    public function data($key = null, $default = null): array
    {
        return $this->only(['first_name', 'last_name', 'national_code', 'mobile']);
    }
}
