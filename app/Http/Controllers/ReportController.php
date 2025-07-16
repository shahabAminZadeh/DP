<?php

namespace App\Http\Controllers;

use App\Models\Expense_requests;
use App\Models\User;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
     public function paymentReport(Request $request)
    {
        // پارامترهای فیلتر
        $filters = [
            'status' => $request->input('status'),
            'category' => $request->input('category'),
            'user' => $request->input('user'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ];

        $query = Expense_requests::with(['user', 'category']);

        // اعمال فیلترها
        if ($filters['status']) {
            $query->where('status', $filters['status']);
        }

        if ($filters['category']) {
            $query->where('category_id', $filters['category']);
        }

        if ($filters['user']) {
            $query->where('user_id', $filters['user']);
        }

        if ($filters['start_date'] && $filters['end_date']) {
            $query->whereBetween('created_at', [
                Carbon::parse($filters['start_date'])->startOfDay(),
                Carbon::parse($filters['end_date'])->endOfDay()
            ]);
        }

        $requests = $query->paginate(20);
        $categories = Expense_requests::all();
        $users = User::all();

        $stats = [
            'total_amount' => $query->sum('amount'),
            'avg_amount' => $query->avg('amount'),
            'max_amount' => $query->max('amount'),
            'min_amount' => $query->min('amount'),
            'pending_count' => Expense_requests::where('status', 'pending')->count(),
            'approved_count' => Expense_requests::where('status', 'approved')->count(),
            'rejected_count' => Expense_requests::where('status', 'rejected')->count(),
            'paid_count' => Expense_requests::where('status', 'paid')->count(),
        ];

        return view('reports.payment', compact(
            'requests',
            'categories',
            'users',
            'filters',
            'stats'
        ));
    }

    public function exportReport(Request $request)
    {
        //  خروجی اکسل  ساده
        $data = Expense_requests::with(['user', 'category'])
            ->where('status', 'paid')
            ->get();

        $csvData = "ID,User,Category,Amount,Sheba,Status,Date\n";

        foreach ($data as $item) {
            $csvData .= sprintf(
                "%s,%s,%s,%s,%s,%s,%s\n",
                $item->id,
                $item->user->name,
                $item->category->name,
                $item->amount,
                $item->sheba,
                $item->status,
                $item->created_at->format('Y-m-d H:i')
            );
        }

        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="payments_report.csv"');
    }
}
