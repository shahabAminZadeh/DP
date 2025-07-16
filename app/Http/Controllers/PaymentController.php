<?php

namespace App\Http\Controllers;

use App\Models\Expense_requests;
use App\Jobs\ProcessPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class PaymentController extends Controller
{
public function manualPayment(Request $request)
{
    $request->validate([
        'request_ids' => 'required|array',
        'request_ids.*' => 'exists:expense_requests,id'
    ]);

    $requests = Expense_requests::whereIn('id', $request->request_ids)
        ->where('status', 'approved') // فقط درخواست‌های تایید شده
        ->get();

    if ($requests->isEmpty()) {
        return redirect()->back()
            ->with('error', 'هیچ درخواست تایید شده‌ای برای پرداخت یافت نشد');
    }

    DB::beginTransaction();

    try {
        foreach ($requests as $expenseRequest) {
            ProcessPayment::dispatch($expenseRequest);
            $expenseRequest->increment('payment_attempts');

            // تغییر وضعیت به "در حال پردازش"
            $expenseRequest->update(['status' => 'processing']);
        }

        DB::commit();
        return redirect()->back()
            ->with('success', "{$requests->count()} درخواست برای پرداخت ارسال شدند");

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()
            ->with('error', 'خطا در پردازش پرداخت: ' . $e->getMessage());
    }
}
}
