<?php

namespace App\Http\Controllers;

use App\Models\Expense_requests;
use Illuminate\Http\Request;

class ApprovedRequestController extends Controller
{
    public function index()
    {
        $requests = Expense_requests::with(['user', 'category'])
            ->where('status', 'approved') // فقط درخواست‌های تایید شده
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('approvals.approved', compact('requests'));
    }
}
