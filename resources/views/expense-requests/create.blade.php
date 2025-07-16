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
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">فرم ثبت درخواست هزینه</h5>
            </div>

            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('expense-requests.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="national_code" class="form-label">کد ملی</label>
                        <input type="text" class="form-control @error('national_code') is-invalid @enderror"
                               id="national_code" name="national_code"
                               value="{{ old('national_code') }}"
                               placeholder="کد ملی خود را وارد کنید">
                        @error('national_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="category_id" class="form-label">نوع هزینه</label>
                        <select class="form-select @error('category_id') is-invalid @enderror"
                                id="category_id" name="category_id">
                            <option value="">-- انتخاب کنید --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">شرح هزینه</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="3"
                                  placeholder="شرح کامل هزینه را وارد کنید">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="amount" class="form-label">مبلغ (تومان)</label>
                        <input type="number" class="form-control @error('amount') is-invalid @enderror"
                               id="amount" name="amount"
                               value="{{ old('amount') }}"
                               placeholder="مبلغ را وارد کنید">
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="sheba" class="form-label">شماره شبا (بدون IR)</label>
                        <input type="text" class="form-control @error('sheba') is-invalid @enderror"
                               id="sheba" name="sheba"
                               value="{{ old('sheba') }}"
                               placeholder="مثال: 11112222333344445555667788">
                        <small class="form-text text-muted">26 رقم - فقط شامل اعداد</small>
                        @error('sheba')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="attachment" class="form-label">پیوست</label>
                        <input type="file" class="form-control @error('attachment') is-invalid @enderror"
                               id="attachment" name="attachment">
                        <small class="form-text text-muted">فرمت‌های مجاز: PDF, JPG, PNG - حداکثر 2MB</small>
                        @error('attachment')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">ثبت درخواست</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
