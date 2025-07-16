<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Expense_requests;
use Illuminate\Support\Facades\Storage;

class ApprovalTest extends TestCase
{
    /** @test */
    public function approver_can_view_pending_requests()
    {
        $response = $this->get(route('approvals.index'));
        $response->assertOk();
    }

    /** @test */
    public function approver_can_approve_requests()
    {
        $request = Expense_requests::factory()->create(['status' => 'pending']);

        $response = $this->post(route('approvals.update-status'), [
            'request_ids' => [$request->id],
            'status' => 'approved'
        ]);

        $response->assertRedirect()
            ->assertSessionHas('success');

        $this->assertEquals('approved', $request->fresh()->status);
    }

    /** @test */
    public function approver_can_reject_requests_with_reason()
    {
        $request = Expense_requests::factory()->create();

        $response = $this->post(route('approvals.update-status'), [
            'request_ids' => [$request->id],
            'status' => 'rejected',
            'rejection_reason' => 'Incomplete documents'
        ]);

        $response->assertRedirect();
        $this->assertEquals('rejected', $request->fresh()->status);
        $this->assertEquals('Incomplete documents', $request->fresh()->rejection_reason);
    }

    /** @test */
    public function user_can_download_request_attachment()
    {
        Storage::fake('public');
        $request = Expense_requests::factory()->create([
            'attachment_path' => 'attachments/test.pdf'
        ]);

        Storage::disk('public')->put('attachments/test.pdf', 'test content');

        $response = $this->get(route('approvals.download', $request->id));
        $response->assertDownload();
    }
}
