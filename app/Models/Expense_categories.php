<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ExpenseRequest;

class Expense_categories extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function expenseRequests()
    {
        return $this->hasMany(Expense_requests::class);
    }
}
