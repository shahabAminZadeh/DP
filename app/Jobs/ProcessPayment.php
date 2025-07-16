<?php

namespace App\Jobs;

use App\Models\Expense_requests;
use App\Services\PaymentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessPayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $expenseRequest;
    public function __construct(Expense_requests $expenseRequest)
    {
         $this->expenseRequest = $expenseRequest;
    }

    public function handle(PaymentService $paymentService)
    {
        Log::info("Starting payment processing for request: {$this->expenseRequest->id}");
        try {
            $result = $paymentService->pay($this->expenseRequest);

            if ($result['success']) {
                $this->expenseRequest->update(['status' => 'paid']);
                Log::info("Payment successful for request: {$this->expenseRequest->id}");
            } else {
                Log::error("Payment failed for request: {$this->expenseRequest->id}. Error: {$result['message']}");
            }

        } catch (\Exception $e) {
            Log::critical("Payment process failed: " . $e->getMessage());
        }
        Log::info("Finished processing request: {$this->expenseRequest->id}");
    }
}
