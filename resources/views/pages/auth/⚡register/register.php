<?php

declare(strict_types=1);

use App\Enums\NationalityType;
use App\Rules\NationalCode;
use App\Rules\NotIranianNationalCode;
use App\Rules\PersianName;
use App\Support\Digits;
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
    /* ------------------------------- فیلدها ------------------------------- */

    public string $first_name_fa = '';

    public string $last_name_fa = '';

    public NationalityType $nationality_type = NationalityType::Iranian;

    public string $identity_number = '';

    public string $mobile_number = '';

    public string $otp_code = '';
    /* ------------------------------- وضعیت ------------------------------- */

    public string $view_state = 'RegisterForm';

    /* ---------------------------- نرمال‌سازی ----------------------------- */

    protected array $normalizers = [
        'first_name_fa' => [PersianText::class, 'name'],
        'last_name_fa' => [PersianText::class, 'name'],
        'identity_number' => [Digits::class, 'onlyDigits'],
        'mobile_number' => [Digits::class, 'onlyDigits'],
        'otp_code' => [Digits::class, 'onlyDigits'],
    ];

    public function updated(string $property): void
    {
        $this->normalizeField($property);
    }

    protected function normalizeField(string $field): void
    {
        if (! isset($this->normalizers[$field])) {
            return;
        }

        $callable = $this->normalizers[$field];
        $this->$field = (string) $callable((string) ($this->$field ?? ''));
    }

    public function normalizeAll(): void
    {
        foreach (array_keys($this->normalizers) as $field) {
            $this->normalizeField($field);
        }
    }

    /* ----------------------------- قوانین ------------------------------- */

    protected function rules(): array
    {
        return [
            'first_name_fa' => ['bail', 'required', 'string', 'min:2', 'max:40', new PersianName],
            'last_name_fa' => ['bail', 'required', 'string', 'min:2', 'max:50', new PersianName],
            'nationality_type' => ['required', Rule::enum(NationalityType::class)],
            'identity_number' => ['bail', 'required', 'string',
                Rule::when(
                    $this->nationality_type === NationalityType::Iranian,
                    ['digits:10', new NationalCode],
                    ['digits_between:6,20', new NotIranianNationalCode],
                ),
            ],
            'mobile_number' => ['bail', 'required', 'digits:11', 'starts_with:09'],
        ];
    }

    public function continueRegister(): void
    {
        $this->normalizeAll();
        $this->validate();
        Flux::modal('otp-verify')->show();
    }
};
