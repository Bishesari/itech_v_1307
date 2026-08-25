<?php

namespace App\Services\Sms;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class ParsGreenSmsService
{
    public function __construct(
        private readonly string $apiKey,
        private readonly string $baseUrl,
        private readonly ?string $defaultSmsNumber = null,
        private readonly int $otpTemplateId = 0,
        private readonly bool $otpAddName = false,
    ) {
    }

    public function sendOtp(string $mobile, string $code, ?int $templateId = null): array
    {
        return $this->post('Message/SendOtp', [
            'Mobile' => $mobile,
            'SmsCode' => $code,
            'TemplateId' => $templateId ?? $this->otpTemplateId,
            'AddName' => $this->otpAddName,
        ]);
    }

    public function sendSms(string $body, array $mobiles, ?string $smsNumber = null): array
    {
        $payload = [
            'SmsBody' => $body,
            'Mobiles' => array_values($mobiles),
        ];

        $number = $smsNumber ?? $this->defaultSmsNumber;

        if (! blank($number)) {
            $payload['SmsNumber'] = $number;
        }

        return $this->post('Message/SendSms', $payload);
    }

    private function post(string $path, array $payload): array
    {
        if (blank($this->apiKey)) {
            throw new RuntimeException('ParsGreen API key is not configured.');
        }

        try {
            $response = Http::baseUrl(rtrim($this->baseUrl, '/') . '/Apiv2')
                ->withHeaders([
                    'authorization' => 'BASIC APIKEY:' . $this->apiKey,
                    'Content-Type' => 'application/json;charset=utf-8',
                ])
                ->timeout(15)
                ->retry(2, 500)
                ->post($path, $payload);
        } catch (ConnectionException $exception) {
            throw new RuntimeException('Could not connect to ParsGreen SMS service.', previous: $exception);
        }

        if (! $response->successful()) {
            throw new RuntimeException('ParsGreen HTTP error: ' . $response->status());
        }

        $data = $response->json();

        if (! is_array($data)) {
            throw new RuntimeException('Invalid ParsGreen response.');
        }

        return $data;
    }
}
