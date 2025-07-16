<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Expense_requests;
use Carbon\Carbon;

class ReportTest extends TestCase
{
    /** @test */
    public function admin_can_view_payment_reports()
    {
        Expense_requests::factory()->create([
            'status' => 'paid',
            'amount' => 2000000,
            'created_at' => Carbon::now()->subDay()
        ]);

        $response = $this->get(route('reports.payment'));
        $response->assertOk()
            ->assertViewHas('stats.total_amount', 2000000);
    }

    /** @test */
    public function admin_can_export_payment_reports_to_csv()
    {
        Expense_requests::factory()->create(['status' => 'paid']);

        $response = $this->get(route('reports.export'));
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->assertDownload('payments_report.csv');
    }
}
