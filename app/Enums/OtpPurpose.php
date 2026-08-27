<?php
declare(strict_types=1);

namespace App\Enums;

enum OtpPurpose: string
{
    case Register = 'register';
    case Login = 'login';
    case ChangeMobile = 'change_mobile';

    public function label(): string
    {
        return match ($this) {
            self::Register => 'ثبت نام',
            self::Login => 'ورود',
            self::ChangeMobile => 'تغییر شماره موبایل',
        };
    }
}
