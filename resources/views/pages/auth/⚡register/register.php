<?php

declare(strict_types=1);

use App\Enums\NationalityType;
use App\Rules\IranianMobile;
use App\Rules\NationalCode;
use App\Rules\NotIranianNationalCode;
use App\Support\PersianText;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new
#[Layout('layouts::auth')]
#[Title('ثبت نام')]
class extends Component
{
    public string $first_name_fa = '';

    public string $last_name_fa = '';

    public NationalityType $nationality_type = NationalityType::Iranian;

    public string $identity_number = '';

    public string $mobile_number = '';

    protected function rules(): array
    {
        return [
            // ✅ فیلد ۱ — نهایی‌شده
            'first_name_fa' => [
                'bail',
                'required',
                'string',
                'regex:'.PersianText::NAME_REGEX,
                'min:2',
                'max:40',
            ],

            // ⏳ فیلدهای بعدی — دست‌نخورده، نوبتی می‌رسیم
            'last_name_fa' => ['required', 'string', 'min:2', 'max:50'],
            'nationality_type' => ['required', Rule::enum(NationalityType::class)],
            'identity_number' => [
                'required',
                'string',
                Rule::when(
                    $this->nationality_type === NationalityType::Iranian,
                    ['digits:10', new NationalCode],
                    ['digits_between:6,20', new NotIranianNationalCode],
                ),
            ],
            'mobile_number' => ['required', 'string', new IranianMobile],
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'first_name_fa' => 'نام',
            'last_name_fa' => 'نام خانوادگی',
            'nationality_type' => 'نوع تابعیت',
            'identity_number' => 'کد ملی / شناسه اختصاصی',
            'mobile_number' => 'شماره موبایل',
        ];
    }

    protected function messages(): array
    {
        return [
            'first_name_fa.required' => 'وارد کردن نام الزامی است.',
            'first_name_fa.regex' => 'نام فقط می‌تواند شامل حروف فارسی، فاصله و نیم‌فاصله باشد.',
            'first_name_fa.min' => 'نام باید حداقل ۲ حرف باشد.',
            'first_name_fa.max' => 'نام نمی‌تواند بیشتر از ۴۰ حرف باشد.',
        ];
    }

    /** نرمال‌سازی هنگام blur هر فیلد */
    public function updatedFirstNameFa(): void
    {
        $this->first_name_fa = PersianText::name($this->first_name_fa);
    }

    /** تور اطمینان قبل از اعتبارسنجی */
    protected function normalize(): void
    {
        $this->first_name_fa = PersianText::name($this->first_name_fa);
    }

    public function continueRegister(): void
    {
        $this->normalize();

        $validated = $this->validate();

        // مرحله بعدی ثبت‌نام...
    }
};
