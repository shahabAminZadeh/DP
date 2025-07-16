<?php

namespace App\Services;

use App\Models\Expense_requests;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    private $bankApis = [
        '11' => 'https://api.bank1.com/pay',
        '22' => 'https://api.bank2.com/transfer',
        '33' => 'https://api.bank3.com/v1/payments',
    ];

    public function pay(Expense_requests $expenseRequest)
    {
        // استخراج کد بانک از شماره شبا (پس از IR)
        $bankCode = substr($expenseRequest->sheba, 4, 2);

        if (!isset($this->bankApis[$bankCode])) {
            return [
                'success' => false,
                'message' => 'بانک پشتیبانی نمیشود'
            ];
        }

        $bankApi = $this->bankApis[$bankCode];

        $payload = [
            'amount' => $expenseRequest->amount,
            'sheba' => $expenseRequest->sheba,
            'reference_id' => $expenseRequest->id,
        ];

        Log::info("ارسال درخواست پرداخت به بانک: {$bankApi}", $payload);

        try {
            // در محیط تست از پاسخ ثابت استفاده می‌کنیم
            if (app()->environment('testing')) {
                $response = ['status' => 'success', 'reference' => 'TEST-REF-' . uniqid()];
            } else {
                $response = Http::post($bankApi, $payload)->json();
            }

            Log::info("پاسخ بانک: " . json_encode($response));

            if ($response['status'] === 'success') {
                return [
                    'success' => true,
                    'message' => 'پرداخت با موفقیت انجام شد',
                    'bank_reference' => $response['reference']
                ];
            }

            return [
                'success' => false,
                'message' => $response['message'] ?? 'خطا در پرداخت'
            ];

        } catch (\Exception $e) {
            Log::error("خطا در ارتباط با بانک: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'خطا در ارتباط با سرویس بانک'
            ];
        }
    }

    public function addBankApi($prefix, $url)
    {
        $this->bankApis[$prefix] = $url;
        return $this;
    }
}
