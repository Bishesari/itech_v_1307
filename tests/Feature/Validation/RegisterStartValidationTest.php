<?php
use App\Rules\IranMobile;
use App\Rules\NationalCode;

it('کد ملی معتبر را می‌پذیرد', function (string $code) {
    expect(NationalCode::isValid($code))->toBeTrue();
})->with(['0499370899', '0790419904', '0084575948']);

it('کد ملی نامعتبر را رد می‌کند', function (string $code) {
    expect(NationalCode::isValid($code))->toBeFalse();
})->with(['1111111111', '0000000000', '1234567890', '123', 'abcdefghij']);

it('فرمت‌های مختلف موبایل را یکسان می‌کند', function (string $input) {
    expect(IranMobile::normalize($input))->toBe('09123456789');
})->with([
    '09123456789',
    '+989123456789',
    '00989123456789',
    '989123456789',
    '9123456789',
    '۰۹۱۲۳۴۵۶۷۸۹',
    '0912 345 6789',
    '0912-345-6789',
]);

it('موبایل نامعتبر را رد می‌کند', function (string $input) {
    expect(IranMobile::normalize($input))->toBeNull();
})->with(['0812345678', '091234567', '091234567890', '']);
