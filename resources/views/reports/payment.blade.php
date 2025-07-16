<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ثبت درخواست هزینه</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; padding-top: 30px; }
        .container { max-width: 700px; }
        .form-label { font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <h1 class="mb-4">گزارش پرداخت‌ها</h1>

    <!-- فرم فیلترها -->
    <div class="card mb-4">
        <div class="card-header">فیلترها</div>
        <div class="card-body">
            <form method="GET" action="{{ route('reports.payment') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label>وضعیت</label>
                        <select name="status" class="form-select">
                            <option value="">همه</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>در انتظار</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>تایید شده</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>رد شده</option>
                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>پرداخت شده</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label>دسته‌بندی</label>
                        <select name="category" class="form-select">
                            <option value="">همه</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label>کاربر</label>
                        <select name="user" class="form-select">
                            <option value="">همه</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->national_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label>از تاریخ</label>
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>

                    <div class="col-md-3">
                        <label>تا تاریخ</label>
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>

                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">اعمال فیلتر</button>
                        <a href="{{ route('reports.payment') }}" class="btn btn-secondary ms-2">پاک کردن</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- آمار کلی -->
    <div class="card mb-4">
        <div class="card-header">آمار کلی</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h5>مجموع مبالغ</h5>
                            <p class="fs-3">{{ number_format($stats['total_amount']) }} تومان</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h5>میانگین</h5>
                            <p class="fs-3">{{ number_format($stats['avg_amount']) }} تومان</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h5>پرداخت شده</h5>
                            <p class="fs-3">{{ $stats['paid_count'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h5>در انتظار</h5>
                            <p class="fs-3">{{ $stats['pending_count'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- جدول گزارش‌ها -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>لیست درخواست‌ها</span>
            <a href="{{ route('reports.export') }}" class="btn btn-success">
                <i class="bi bi-file-earmark-spreadsheet"></i> خروجی Excel
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>کاربر</th>
                            <th>دسته‌بندی</th>
                            <th>مبلغ</th>
                            <th>شبا</th>
                            <th>وضعیت</th>
                            <th>تاریخ ثبت</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requests as $request)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $request->user->name }}</td>
                            <td>{{ $request->category->name }}</td>
                            <td>{{ number_format($request->amount) }}</td>
                            <td>{{ $request->sheba }}</td>
                            <td>
                                <span class="badge
                                    @if($request->status == 'pending') bg-warning
                                    @elseif($request->status == 'approved') bg-primary
                                    @elseif($request->status == 'rejected') bg-danger
                                    @elseif($request->status == 'paid') bg-success @endif">
                                    {{ $request->status }}
                                </span>
                            </td>
                            <td>{{ $request->created_at->format('Y/m/d H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $requests->appends(request()->query())->links() }}
        </div>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
