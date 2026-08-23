<?php

declare(strict_types=1);

use App\Enums\NationalityType;
use App\Rules\NationalCode;
use App\Rules\NotIranianNationalCode;
use App\Rules\PersianOnly;
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
            'first_name_fa' => ['required', 'string', 'min:2', 'max:40', new PersianOnly],
            'last_name_fa' => ['required', 'string', 'min:2', 'max:50', new PersianOnly],
            'nationality_type' => ['required', Rule::enum(NationalityType::class)],
            'identity_number' => [
                'required',
                'string',
                Rule::when(
                    $this->nationality_type === NationalityType::Iranian,
                    ['digits:10', new NationalCode],   // ایرانی
                    ['digits_between:6,20', new NotIranianNationalCode]  // اتباع
                ),
            ],
            'mobile_number' => [
                'required',
                'string',
                'digits:11',
                'regex:/^09\d{9}$/',
            ],

        ];
    }

    public function continueRegister(): void
    {
        $this->validate();
    }
};
