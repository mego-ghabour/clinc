<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير مخبري - {{ $labRequest->request_number }} - {{ $labRequest->patient->name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Cairo', sans-serif;
        }
        body {
            background-color: #f8fafc;
            color: #1e293b;
            margin: 0;
            padding: 20px;
        }
        .report-card {
            background: #fff;
            max-width: 800px;
            margin: 0 auto;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #0d9488;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }
        .clinic-info h1 {
            margin: 0 0 5px 0;
            color: #0f766e;
            font-size: 24px;
            font-weight: 900;
        }
        .clinic-info p {
            margin: 0;
            font-size: 11px;
            color: #64748b;
            font-weight: 600;
        }
        .report-badge {
            text-align: left;
        }
        .report-badge .title {
            font-size: 18px;
            font-weight: 900;
            color: #0f766e;
            margin: 0;
        }
        .report-badge .req-no {
            font-family: monospace;
            font-size: 14px;
            font-weight: 700;
            color: #475569;
            margin-top: 4px;
        }
        .patient-box {
            background: #f0fdfa;
            border: 1px solid #ccfbf1;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 25px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            font-size: 12px;
        }
        .patient-box .label {
            color: #64748b;
            font-weight: 700;
            font-size: 10px;
            display: block;
        }
        .patient-box .val {
            font-weight: 700;
            color: #0f172a;
        }
        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            font-size: 12px;
        }
        .results-table th {
            background: #f1f5f9;
            color: #475569;
            font-weight: 700;
            padding: 10px 12px;
            text-align: right;
            border-bottom: 2px solid #cbd5e1;
        }
        .results-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #e2e8f0;
        }
        .results-table tr.abnormal {
            background-color: #fff1f2;
        }
        .results-table tr.abnormal .res-val {
            color: #e11d48;
            font-weight: 900;
        }
        .flag-tag {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 700;
        }
        .flag-normal {
            background: #dcfce7;
            color: #15803d;
        }
        .flag-abnormal {
            background: #ffe4e6;
            color: #be123c;
        }
        .footer-signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px dashed #cbd5e1;
        }
        .sig-block {
            text-align: center;
            width: 200px;
        }
        .sig-block p {
            margin: 5px 0 0 0;
            font-size: 11px;
            color: #64748b;
            font-weight: 700;
        }
        .sig-line {
            height: 40px;
            border-bottom: 1px solid #94a3b8;
            margin-bottom: 5px;
        }
        .no-print-bar {
            max-width: 800px;
            margin: 0 auto 15px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .print-btn {
            background: #0d9488;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .print-btn:hover {
            background: #0f766e;
        }
        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .no-print-bar {
                display: none !important;
            }
            .report-card {
                box-shadow: none;
                border: none;
                padding: 20px;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

    <div class="no-print-bar">
        <a href="{{ route('lab-requests.show', $labRequest->id) }}" style="text-decoration: none; color: #475569; font-size: 13px; font-weight: 700;">
            <i class="fa-solid fa-arrow-right"></i> العودة للطلب
        </a>
        <button onclick="window.print()" class="print-btn">
            <i class="fa-solid fa-print"></i> طباعة التقرير الطبي
        </button>
    </div>

    <div class="report-card">
        <!-- Header -->
        <div class="header">
            <div class="clinic-info">
                <h1>{{ $settings['clinic_name'] ?? 'مجمع العيادات الطبية' }}</h1>
                <p>قسم المختبر والتحاليل الطبية المتقدمة</p>
                <p>هاتف: {{ $settings['clinic_phone'] ?? '—' }} | البريد: {{ $settings['clinic_email'] ?? '—' }}</p>
                <p>العنوان: {{ $settings['clinic_address'] ?? '—' }}</p>
            </div>
            <div class="report-badge">
                <h2 class="title">تقرير التحاليل الطبية</h2>
                <div class="req-no">REQ: {{ $labRequest->request_number }}</div>
                <div style="font-size: 11px; color: #64748b; margin-top: 4px;">
                    التاريخ: {{ now()->format('Y-m-d') }}
                </div>
            </div>
        </div>

        <!-- Patient Info Grid -->
        <div class="patient-box">
            <div>
                <span class="label">اسم المريض</span>
                <span class="val">{{ $labRequest->patient->name }}</span>
            </div>
            <div>
                <span class="label">رقم الملف الطبي (MRN)</span>
                <span class="val" style="font-family: monospace;">{{ $labRequest->patient->mrn }}</span>
            </div>
            <div>
                <span class="label">العمر / الجنس</span>
                <span class="val">{{ \Carbon\Carbon::parse($labRequest->patient->date_of_birth)->age }} سنة / {{ $labRequest->patient->gender == 'M' || $labRequest->patient->gender == 'ذكر' ? 'ذكر' : 'أنثى' }}</span>
            </div>
            <div>
                <span class="label">الطبيب المعالج</span>
                <span class="val">د. {{ $labRequest->doctor->name ?? 'طلب مباشر' }}</span>
            </div>
            <div>
                <span class="label">تاريخ ووقت سحب العينة</span>
                <span class="val">{{ $labRequest->sample_collected_at ? $labRequest->sample_collected_at->format('Y-m-d H:i') : '—' }}</span>
            </div>
            <div>
                <span class="label">تاريخ إصدار التقرير</span>
                <span class="val">{{ $labRequest->completed_at ? $labRequest->completed_at->format('Y-m-d H:i') : now()->format('Y-m-d H:i') }}</span>
            </div>
            <div>
                <span class="label">الأولوية</span>
                <span class="val">{{ $labRequest->priority }}</span>
            </div>
            <div>
                <span class="label">أخصائي المختبر</span>
                <span class="val">{{ $labRequest->technician->name ?? 'مختبر سريري' }}</span>
            </div>
        </div>

        @if($labRequest->clinical_notes)
        <div style="margin-bottom: 20px; font-size: 11px; background: #f8fafc; padding: 10px 15px; border-radius: 6px; border-right: 3px solid #0d9488;">
            <strong style="color: #0f766e;">ملاحظات سريرية:</strong> {{ $labRequest->clinical_notes }}
        </div>
        @endif

        <!-- Results Table -->
        <table class="results-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 35%;">الفحص المخبري (Test Name)</th>
                    <th style="width: 20%;">النتيجة (Result)</th>
                    <th style="width: 15%;">الوحدة (Unit)</th>
                    <th style="width: 25%;">المعدل الطبيعي (Reference)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($labRequest->items as $index => $item)
                <tr class="{{ $item->is_abnormal ? 'abnormal' : '' }}">
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $item->test_name }}</strong>
                        @if($item->notes)
                            <div style="font-size: 10px; color: #64748b;">{{ $item->notes }}</div>
                        @endif
                    </td>
                    <td>
                        <span class="res-val" style="font-family: monospace; font-size: 13px;">
                            {{ $item->result_value ?? 'لم تسجل' }}
                        </span>
                        @if($item->is_abnormal)
                            <span class="flag-tag flag-abnormal">غير طبيعي *</span>
                        @endif
                    </td>
                    <td style="font-family: monospace; color: #64748b;">
                        {{ $item->unit ?? '—' }}
                    </td>
                    <td style="font-family: monospace; color: #64748b;">
                        {{ $item->normal_range ?? '—' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Footer Signatures -->
        <div class="footer-signatures">
            <div class="sig-block">
                <div class="sig-line"></div>
                <p>توقيع فني المختبر</p>
                <p style="font-size: 10px; color: #94a3b8;">{{ $labRequest->technician->name ?? 'Medical Lab Specialist' }}</p>
            </div>
            <div class="sig-block">
                <div class="sig-line"></div>
                <p>ختم واعتماد المدير الطبي</p>
                <p style="font-size: 10px; color: #94a3b8;">{{ $settings['clinic_name'] ?? 'Clinic Director' }}</p>
            </div>
        </div>

        <div style="margin-top: 30px; text-align: center; font-size: 9px; color: #94a3b8; border-top: 1px solid #f1f5f9; padding-top: 10px;">
            هذا التقرير صادر إلكترونياً من نظام Clinc الطبي ويعتبر معتمداً رسمياً. تم طباعته في {{ now()->format('Y-m-d H:i:s') }}
        </div>
    </div>

</body>
</html>
