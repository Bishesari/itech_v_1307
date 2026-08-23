<?php

declare(strict_types=1);

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

    public string $nationality_type = '1';

    public string $identity_number = '';

    public string $mobile_number = '';

    public function continueRegister(): void
    {
        $validated = $this->validate([
            'first_name_fa' => ['required', 'string', 'max:100'],
            'last_name_fa' => ['required', 'string', 'max:100'],
            'nationality_type' => ['required', 'in:1,2'],
            'identity_number' => [
                'required',
                'string',
                $this->nationality_type === '1' ? 'digits:10' : 'max:20',
            ],
            'mobile_number' => [
                'required',
                'regex:/^09\d{9}$/',
            ],
        ]);

        // مرحله بعد: تولید و ارسال OTP
    }

};
