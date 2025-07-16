{{-- resources/views/approvals/approved.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4 text-center">درخواست‌های تایید شده</h1>

    <div class="card shadow-sm">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">لیست درخواست‌های تایید شده</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>نام کاربر</th>
                            <th>دسته‌بندی</th>
                            <th>مبلغ (تومان)</th>
                            <th>شبا</th>
                            <th>تاریخ تایید</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($requests as $req)
                        <tr>
                            <td>{{ $req->user->name }}</td>
                            <td>{{ $req->category->name }}</td>
                            <td>{{ number_format($req->amount) }}</td>
                            <td>{{ $req->sheba }}</td>
                            <td>{{ $req->updated_at->format('Y/m/d H:i') }}</td>
                            <td>
                                <form action="{{ route('payments.process-manual') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="request_ids[]" value="{{ $req->id }}">
                                    <button type="submit" class="btn btn-sm btn-success">پرداخت دستی</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">هیچ درخواست تایید شده‌ای وجود ندارد</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
