<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Expense_requests;
use Illuminate\Support\Facades\Queue;

class PaymentTest extends TestCase
{
    /** @test */
    public function admin_can_process_manual_payment_for_approved_requests()
    {
        Queue::fake();

        $request = Expense_requests::factory()->create(['status' => 'approved']);

        $response = $this->post(route('payments.process-manual'), [
            'request_ids' => [$request->id]
        ]);

        Queue::assertPushed(\App\Jobs\ProcessPayment::class);
        $response->assertRedirect()
            ->assertSessionHas('success');
    }

    /** @test */
    public function cannot_process_payment_for_non_approved_requests()
    {
        $request = Expense_requests::factory()->create(['status' => 'pending']);

        $response = $this->post(route('payments.process-manual'), [
            'request_ids' => [$request->id]
        ]);

        $response->assertRedirect()
            ->assertSessionHas('error');
    }

    /** @test */
    public function admin_can_view_approved_requests_for_payment()
    {
        Expense_requests::factory()->create(['status' => 'approved']);

        $response = $this->get(route('approved-requests.index'));
        $response->assertOk();
    }
}
