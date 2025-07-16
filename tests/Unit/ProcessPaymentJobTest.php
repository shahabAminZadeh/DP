<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Jobs\ProcessPayment;
use App\Models\Expense_requests;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Log;

class ProcessPaymentJobTest extends TestCase
{
    /** @test */
    public function job_processes_successful_payment()
    {
        Log::spy();
        $request = Expense_requests::factory()->create();

        $paymentService = $this->mock(PaymentService::class);
        $paymentService->shouldReceive('pay')
            ->andReturn(['success' => true]);

        (new ProcessPayment($request))->handle($paymentService);

        $this->assertEquals('paid', $request->fresh()->status);
        Log::shouldHaveReceived('info')->with("Payment successful for request: {$request->id}");
    }

    /** @test */
    public function job_handles_failed_payment()
    {
        Log::spy();
        $request = Expense_requests::factory()->create();

        $paymentService = $this->mock(PaymentService::class);
        $paymentService->shouldReceive('pay')
            ->andReturn(['success' => false, 'message' => 'Insufficient funds']);

        (new ProcessPayment($request))->handle($paymentService);

        Log::shouldHaveReceived('error')->with("Payment failed for request: {$request->id}. Error: Insufficient funds");
    }
}
