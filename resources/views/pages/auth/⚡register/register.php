<?php

declare(strict_types=1);

use App\Enums\NationalityType;
use App\Rules\NationalCode;
use App\Rules\NotIranianNationalCode;
use App\Rules\PersianName;
use App\Services\Sms\ParsGreenSmsService;
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
    public string $first_name_fa = '';

    public string $last_name_fa = '';

    public NationalityType $nationality_type = NationalityType::Iranian;

    public string $identity_number = '';

    public string $mobile_number = '';

    protected ParsGreenSmsService $sms;

    public function boot(ParsGreenSmsService $sms): void
    {
        $this->sms = $sms;
    }

    // نگاشت نام فیلد → تابع نرمال‌ساز
    protected array $normalizers = [
        'first_name_fa' => [PersianText::class, 'name'],
        'last_name_fa' => [PersianText::class, 'name'],
        'identity_number' => [Digits::class, 'onlyDigits'],
        'mobile_number' => [Digits::class, 'onlyDigits'],
    ];

    // متد عمومی Livewire برای هر تغییر
    public function updated($property): void
    {
        $this->normalizeField($property);
    }

    // نرمال‌سازی یک فیلد مشخص
    protected function normalizeField(string $field): void
    {
        if (! isset($this->normalizers[$field])) {
            return;
        }
        $callable = $this->normalizers[$field];
        $value = (string) ($this->$field ?? '');
        $this->$field = (string) $callable($value); // cast نهایی
    }

    public function normalizeAll(): void
    {
        foreach (array_keys($this->normalizers) as $field) {
            $this->normalizeField($field);
        }
    }

    public function continueRegister(): void
    {
        $this->normalizeAll();
        $this->validate();

        $this->sendNormalSms();

        // مرحله بعدی ثبت‌نام...
    }

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

    public function sendOtp(): void
    {
        $result = $this->sms->sendOtp(
            mobile: $this->mobile_number,
            code: '1234567',
        );

        if (! ($result['R_Success'] ?? false)) {
            $this->addError('mobile_number', $result['R_Message'] ?? 'ارسال کد تأیید ناموفق بود.');

            return;
        }

        session()->flash('success', 'کد تأیید ارسال شد.');
    }

    public function sendNormalSms(): void
    {

        $result = $this->sms->sendSms(
            body: '12222233',
            mobiles: [$this->mobile_number],
        );

        if (! ($result['R_Success'] ?? false)) {
            $this->addError('message', $result['R_Message'] ?? 'ارسال پیامک ناموفق بود.');

            return;
        }

        session()->flash('success', 'پیامک با موفقیت ارسال شد.');
    }
};
