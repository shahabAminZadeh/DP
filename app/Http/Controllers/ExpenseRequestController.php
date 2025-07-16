<?php

namespace App\Http\Controllers;


use App\Models\Expense_categories;
use App\Models\Expense_requests;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ExpenseRequestController extends Controller
{
    public function create()
    {
        $categories = Expense_categories::all();
        return view('expense-requests.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);

        if ($validated->fails()) {
            return redirect()->back()->withErrors($validated)->withInput();
        }

        $this->createExpenseRequest($validated->validated());

        return redirect()->route('expense-requests.create')
            ->with('success', 'درخواست با موفقیت ثبت شد!');
    }

    public function storeApi(Request $request)
    {
        $validated = $this->validateRequest($request);

        if ($validated->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validated->errors()
            ], 422);
        }

        $expenseRequest = $this->createExpenseRequest($validated->validated());

        return response()->json([
            'message' => 'درخواست با موفقیت ثبت شد',
            'data' => $expenseRequest
        ], 201);
    }

    private function validateRequest(Request $request)
    {
        return Validator::make($request->all(), [
            'national_code' => [
                'required',
                'string',
                'max:10',
                Rule::exists('users', 'national_code')
            ],
            'category_id' => 'required|exists:expense_categories,id',
            'description' => 'required|string|max:500',
            'amount' => 'required|numeric|min:1000|max:100000000',
            'sheba' => [
                'required',
                'string',
                'size:26',
                'regex:/^[0-9]+$/',
                function ($attribute, $value, $fail) {
                    $bankPrefix = substr($value, 0, 2);
                    if (!in_array($bankPrefix, ['11', '22', '33'])) {
                        $fail('شماره شبا متعلق به بانک پشتیبانی شده نیست');
                    }
                }
            ],
            'attachment' => 'nullable|file|mimes:pdf,jpg,png|max:2048'
        ], [
            'national_code.exists' => 'کاربری با این کد ملی یافت نشد',
            'sheba.regex' => 'شماره شبا باید فقط شامل اعداد باشد',
        ]);
    }

    private function createExpenseRequest(array $validatedData)
    {
        $user = User::where('national_code', $validatedData['national_code'])->first();

        $attachmentPath = null;
        if (request()->hasFile('attachment')) {
            $attachmentPath = request()->file('attachment')->store('attachments', 'public');
        }

        return Expense_requests::create([
            'user_id' => $user->id,
            'category_id' => $validatedData['category_id'],
            'description' => $validatedData['description'],
            'amount' => $validatedData['amount'],
            'sheba' => $validatedData['sheba'],
            'attachment_path' => $attachmentPath,
            'status' => 'pending'
        ]);
    }
}
