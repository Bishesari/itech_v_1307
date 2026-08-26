<?php
use App\Services\Sms\ParsGreenSmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::post('/test/send-normal-sms', function (Request $request, ParsGreenSmsService $sms) {
    $validated = $request->validate([
        'mobile' => ['required', 'string', 'regex:/^09\d{9}$/'],
    ]);

    try {
        $result = $sms->sendSms(
            body: 'سلام، این یک پیامک تستی از سایت است.',
            mobiles: [$validated['mobile']],
        );

        if (! ($result['R_Success'] ?? false)) {
            Log::warning('ParsGreen test SMS failed', [
                'mobile' => $validated['mobile'],
                'response' => $result,
            ]);

            return response()->json([
                'ok' => false,
                'message' => $result['R_Message'] ?? 'ارسال پیامک ناموفق بود.',
            ], 422);
        }

        return response()->json([
            'ok' => true,
            'message' => 'پیامک تستی ارسال شد.',
            'result' => $result,
        ]);
    } catch (Throwable $e) {
        Log::error('ParsGreen test SMS exception', [
            'mobile' => $validated['mobile'],
            'message' => $e->getMessage(),
        ]);

        return response()->json([
            'ok' => false,
            'message' => 'خطا در ارتباط با سرویس پیامک.',
        ], 500);
    }
})
    ->middleware(['web', 'throttle:5,1'])
    ->name('test.send-normal-sms');
