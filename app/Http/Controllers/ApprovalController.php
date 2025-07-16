<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense_requests;
use Illuminate\Support\Facades\Storage;
use App\Notifications\ExpenseRequestStatusChanged;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class ApprovalController extends Controller
{
    // app/Http/Controllers/ApprovalController.php

public function index()
{
    // اصلاح: نمایش درخواست‌های در انتظار تایید
    $requests = Expense_requests::with(['user', 'category'])
        ->where('status', 'pending') // فقط درخواست‌های در انتظار تایید
        ->orderBy('created_at', 'desc')
        ->paginate(10);

    return view('approvals.index', compact('requests'));
}

public function updateStatus(Request $request)
{
        $request->validate([
            'request_ids' => 'required|array',
            'request_ids.*' => 'exists:expense_requests,id',
            'status' => 'required|in:approved,rejected',
            'rejection_reason' => 'required_if:status,rejected|nullable|string|max:500'
        ]);

        $status = $request->status;
        $reason = $request->rejection_reason;

        $requests = Expense_requests::whereIn('id', $request->request_ids)->get();

        foreach ($requests as $expenseRequest) {
            $expenseRequest->update([
                'status' => $status,
                'rejection_reason' => $status === 'rejected' ? $reason : null
            ]);

            // ارسال نوتیفیکیشن به کاربر
            $this->sendNotification($expenseRequest);
        }

        return redirect()->route('approvals.index')
            ->with('success', 'تغییر وضعیت درخواست‌ها با موفقیت انجام شد.');
    }
    public function downloadAttachment($id)
        {
            $expenseRequest = Expense_requests::findOrFail($id);

            if (!$expenseRequest->attachment_path) {
                abort(404, 'فایل پیوست وجود ندارد');
            }

            $filePath = storage_path('app/public/' . $expenseRequest->attachment_path);

            // بررسی وجود فیزیکی فایل
            if (!file_exists($filePath)) {
                abort(404, 'فایل در سرور یافت نشد');
            }

            // دانلود با استفاده از response
            return response()->download($filePath);
        }

    private function sendNotification(Expense_requests $expenseRequest)
    {
        try {
            $user = $expenseRequest->user;

            // در حالت واقعی از ایمیل یا پیامک استفاده می‌شود
            // اما در این تست فقط در لاگ ثبت می‌کنیم
            $message = "وضعیت درخواست شما به مبلغ {$expenseRequest->amount} تومان تغییر کرد: ";
            $message .= $expenseRequest->status === 'approved'
                ? 'تایید شد'
                : 'رد شد. دلیل: ' . $expenseRequest->rejection_reason;

            // کامنت کردن ارسال واقعی ایمیل/پیامک
            /*
            Notification::send($user, new ExpenseRequestStatusChanged(
                $expenseRequest->status,
                $expenseRequest->rejection_reason
            ));
            */

            // ثبت در لاگ برای تست
            Log::info("Notification sent to user {$user->id}: " . $message);

        } catch (\Exception $e) {
            Log::error("Failed to send notification: " . $e->getMessage());
        }
    }
}
