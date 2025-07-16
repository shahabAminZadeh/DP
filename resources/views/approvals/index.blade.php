@php use Morilog\Jalali\Jalalian; @endphp

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>کارتابل تایید درخواست‌ها</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; padding-top: 20px; }
        .container { max-width: 1400px; }
        .table th { background-color: #e9ecef; }
        .form-check-input { cursor: pointer; }
        .form-check-label { cursor: pointer; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4 text-center">کارتابل تایید درخواست‌های هزینه</h1>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('approvals.update-status') }}" method="POST" id="approvalForm">
            @csrf

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">لیست درخواست‌های در انتظار تایید</h5>
                    <div>
                        <button type="button" class="btn btn-light btn-sm" id="selectAllBtn">انتخاب همه</button>
                        <button type="button" class="btn btn-light btn-sm ms-2" id="deselectAllBtn">لغو انتخاب همه</button>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="50">#</th>
                                    <th>نام کاربر</th>
                                    <th>کد ملی</th>
                                    <th>دسته‌بندی</th>
                                    <th>شرح</th>
                                    <th>مبلغ (تومان)</th>
                                    <th>شبا</th>
                                    <th>پیوست</th>
                                    <th>تاریخ ثبت</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($requests as $req)
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input request-checkbox"
                                                    type="checkbox"
                                                    name="request_ids[]"
                                                    value="{{ $req->id }}"
                                                    id="req_{{ $req->id }}">
                                            </div>
                                        </td>
                                        <td>{{ $req->user->name }}</td>
                                        <td>{{ $req->user->national_code }}</td>
                                        <td>{{ $req->category->name }}</td>
                                        <td>{{ Str::limit($req->description, 50) }}</td>
                                        <td>{{ number_format($req->amount) }}</td>
                                        <td>{{ $req->sheba }}</td>
                                        <td>
                                            @if($req->attachment_path)
                                                <a href="{{ route('approvals.download', $req->id) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-download"></i> دانلود
                                                </a>
                                            @else
                                                <span class="text-muted">بدون پیوست</span>
                                            @endif
                                        </td>
                                        <td>{{ Jalalian::fromCarbon($req->created_at)->format('Y/m/d H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">هیچ درخواستی در انتظار تایید وجود ندارد</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">عملیات گروهی</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">عملیات</label>
                            <div class="d-flex">
                                <div class="form-check me-4">
                                    <input class="form-check-input" type="radio" name="status"
                                        id="approveRadio" value="approved" checked>
                                    <label class="form-check-label" for="approveRadio">
                                        تایید درخواست‌ها
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status"
                                        id="rejectRadio" value="rejected">
                                    <label class="form-check-label" for="rejectRadio">
                                        رد درخواست‌ها
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3" id="reasonField" style="display: none;">
                            <label for="rejection_reason" class="form-label">دلیل رد</label>
                            <textarea class="form-control" name="rejection_reason"
                                id="rejection_reason" rows="2" placeholder="دلیل رد درخواست را وارد کنید"></textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary px-5">اعمال تغییرات</button>
                    </div>
                </div>
            </div>
        </form>

        <!-- بخش پرداخت دستی - اصلاح شده -->
        <div class="card shadow-sm mt-4">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0">پرداخت دستی</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('payments.process-manual') }}" method="POST" id="manualPaymentForm">
            @csrf
            <input type="hidden" name="request_ids" id="manual_payment_ids">

            <!-- نمایش درخواست‌های انتخاب شده -->
            <div class="mb-3">
                <h6>درخواست‌های انتخاب شده:</h6>
                <ul id="selectedRequestsList" class="list-group mb-3"></ul>
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-0">تعداد: <span id="selectedCount">0</span> درخواست</p>
                    <small class="text-muted">فقط درخواست‌های تایید شده پرداخت می‌شوند</small>
                </div>
                <button type="button" id="manualPaymentBtn" class="btn btn-success px-4">
                    پرداخت دستی
                </button>
            </div>
        </form>
    </div>
</div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // تابع به‌روزرسانی لیست درخواست‌های انتخاب شده
    function updateSelectedRequests() {
        const selectedList = document.getElementById('selectedRequestsList');
        const selectedCount = document.getElementById('selectedCount');
        selectedList.innerHTML = '';

        let count = 0;
        let ids = [];

        document.querySelectorAll('.request-checkbox:checked').forEach(checkbox => {
            const requestId = checkbox.value;
            const row = checkbox.closest('tr');
            const userName = row.cells[1].textContent;
            const amount = row.cells[5].textContent;

            const listItem = document.createElement('li');
            listItem.className = 'list-group-item d-flex justify-content-between';
            listItem.innerHTML = `
                <span>${userName}</span>
                <span>${amount} تومان</span>
                <span>شناسه: ${requestId}</span>
            `;

            selectedList.appendChild(listItem);
            ids.push(requestId);
            count++;
        });

        selectedCount.textContent = count;
        document.getElementById('manual_payment_ids').value = ids.join(',');
    }

    // افزودن رویداد به چک‌باکس‌ها
    document.querySelectorAll('.request-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedRequests);
    });

    // پرداخت دستی
    document.getElementById('manualPaymentBtn').addEventListener('click', function() {
        const selectedIds = document.getElementById('manual_payment_ids').value;

        if (!selectedIds) {
            alert('لطفاً حداقل یک درخواست را انتخاب کنید');
            return;
        }

        document.getElementById('manualPaymentForm').submit();
    });

    // بارگذاری اولیه
    document.addEventListener('DOMContentLoaded', updateSelectedRequests);
</script>
</body>
</html>
