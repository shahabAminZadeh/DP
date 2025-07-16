<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExpenseRequestController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\ApprovedRequestController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('/expense-requests/create', [ExpenseRequestController::class, 'create'])->name('expense-requests.create');
Route::post('/expense-requests', [ExpenseRequestController::class, 'store'])->name('expense-requests.store');


Route::post('/expense-requests/Api', [ExpenseRequestController::class, 'storeApi']);


Route::get('/approvals', [ApprovalController::class, 'index'])->name('approvals.index');
Route::post('/approvals/update-status', [ApprovalController::class, 'updateStatus'])->name('approvals.update-status');
Route::get('/approvals/download-attachment/{id}', [ApprovalController::class, 'downloadAttachment'])->name('approvals.download');
Route::get('/approved-requests', [ApprovedRequestController::class, 'index'])->name('approved-requests.index');

// پرداخت دستی
Route::post('/payments/process-manual', [PaymentController::class, 'manualPayment'])->name('payments.process-manual');



Route::get('/reports/payment', [ReportController::class, 'paymentReport'])->name('reports.payment');
Route::get('/reports/export', [ReportController::class, 'exportReport'])->name('reports.export');

