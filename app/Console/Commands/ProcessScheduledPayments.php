<?php

namespace App\Console\Commands;

use App\Models\Expense_requests;
use Illuminate\Console\Command;
use App\Jobs\ProcessPayment;

class ProcessScheduledPayments extends Command
{
    protected $signature = 'payments:process';
    protected $description = 'Process approved payment requests';

    public function handle()
    {
        $requests = Expense_requests::where('status', 'approved')
            ->where('payment_attempts', '<', 3) // حداکثر 3 بار تلاش
            ->get();

        if ($requests->isEmpty()) {
            $this->info('No pending payments found.');
            return;
        }

        $this->info("Processing {$requests->count()} payment requests...");

        foreach ($requests as $request) {
            ProcessPayment::dispatch($request);
            $request->increment('payment_attempts');
            $this->info("Dispatching payment for request: {$request->id}");
        }

        $this->info('All payments dispatched successfully.');
    }
}
