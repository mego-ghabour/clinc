<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نموذج مطالبة تأمين - {{ $insuranceClaim->claim_number }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        * { box-sizing: border-box; font-family: 'Cairo', sans-serif; }
        body { background-color: #f8fafc; color: #1e293b; margin: 0; padding: 20px; }
        .claim-card {
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
            margin-bottom: 20px;
        }
        .clinic-info h1 { margin: 0 0 5px 0; color: #0f766e; font-size: 22px; font-weight: 900; }
        .clinic-info p { margin: 0; font-size: 11px; color: #64748b; font-weight: 600; }
        .claim-badge { text-align: left; }
        .claim-badge .title { font-size: 16px; font-weight: 900; color: #0f766e; margin: 0; }
        .claim-badge .claim-no { font-family: monospace; font-size: 13px; font-weight: 700; color: #475569; margin-top: 4px; }
        
        .section-title {
            font-size: 12px;
            font-weight: 900;
            color: #0f766e;
            background: #f0fdfa;
            padding: 6px 12px;
            border-radius: 6px;
            border-right: 3px solid #0d9488;
            margin: 20px 0 10px 0;
        }
        .grid-box {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            background: #fafafa;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 11px;
        }
        .grid-box .label { color: #6b7280; font-weight: 700; font-size: 10px; display: block; }
        .grid-box .val { font-weight: 700; color: #111827; }

        .claim-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 12px;
        }
        .claim-table th {
            background: #f3f4f6;
            color: #374151;
            font-weight: 700;
            padding: 10px;
            text-align: right;
            border: 1px solid #e5e7eb;
        }
        .claim-table td {
            padding: 10px;
            border: 1px solid #e5e7eb;
        }
        .footer-signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px dashed #cbd5e1;
        }
        .sig-block { text-align: center; width: 180px; }
        .sig-block p { margin: 4px 0 0 0; font-size: 11px; color: #64748b; font-weight: 700; }
        .sig-line { height: 35px; border-bottom: 1px solid #94a3b8; margin-bottom: 4px; }
        .no-print-bar {
            max-width: 800px;
            margin: 0 auto 15px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .print-btn {
            background: #0d9488; color: #fff; border: none; padding: 8px 18px; border-radius: 8px; font-weight: 700; cursor: pointer; font-size: 12px; display: flex; align-items: center; gap: 6px;
        }
        @media print {
            body { background: #fff; padding: 0; }
            .no-print-bar { display: none !important; }
            .claim-card { box-shadow: none; border: none; padding: 15px; max-width: 100%; }
        }
    </style>
</head>
<body>

    <div class="no-print-bar">
        <a href="{{ route('insurance-claims.show', $insuranceClaim->id) }}" style="text-decoration: none; color: #475569; font-size: 13px; font-weight: 700;">
            <i class="fa-solid fa-arrow-right"></i> العودة للمطالبة
        </a>
        <button onclick="window.print()" class="print-btn">
            <i class="fa-solid fa-print"></i> طباعة نموذج المطالبة
        </button>
    </div>

    <div class="claim-card">
        <!-- Header -->
        <div class="header">
            <div class="clinic-info">
                <h1>{{ $settings['clinic_name'] ?? 'مجمع العيادات الطبية' }}</h1>
                <p>قسم التأمين والمطالبات الصحية (Insurance Billing Dept)</p>
                <p>هاتف: {{ $settings['clinic_phone'] ?? '—' }} | العنوان: {{ $settings['clinic_address'] ?? '—' }}</p>
            </div>
            <div class="claim-badge">
                <h2 class="title">نموذج مطالبة تأمين طبي معتمد</h2>
                <div class="claim-no">CLAIM: {{ $insuranceClaim->claim_number }}</div>
                <div style="font-size: 10px; color: #64748b; margin-top: 2px;">تاريخ المطالبة: {{ $insuranceClaim->claim_date->format('Y-m-d') }}</div>
            </div>
        </div>

        <!-- 1. Patient & Policy Information -->
        <div class="section-title">1. بيانات المستفيد ووثيقة التأمين الصحي:</div>
        <div class="grid-box">
            <div>
                <span class="label">اسم المريض</span>
                <span class="val">{{ $insuranceClaim->patient->name }}</span>
            </div>
            <div>
                <span class="label">رقم الملف الطبي (MRN)</span>
                <span class="val" style="font-family: monospace;">{{ $insuranceClaim->patient->mrn }}</span>
            </div>
            <div>
                <span class="label">الهوية الوطنية / الإقامة</span>
                <span class="val" style="font-family: monospace;">{{ $insuranceClaim->patient->id_number ?? '—' }}</span>
            </div>
            <div>
                <span class="label">شركة التأمين</span>
                <span class="val">{{ $insuranceClaim->insuranceCompany->name }}</span>
            </div>
            <div>
                <span class="label">رمز شركة التأمين</span>
                <span class="val" style="font-family: monospace;">{{ $insuranceClaim->insuranceCompany->code }}</span>
            </div>
            <div>
                <span class="label">فئة التغطية / التحمل</span>
                <span class="val">{{ $insuranceClaim->patient->activeInsurance->class ?? 'Class A' }}</span>
            </div>
        </div>

        <!-- 2. Medical & Physician Details -->
        <div class="section-title">2. بيانات الطبيب والزيارة الطبية:</div>
        <div class="grid-box">
            <div>
                <span class="label">الطبيب المعالج</span>
                <span class="val">د. {{ $insuranceClaim->doctor->name ?? 'طبيب عام' }}</span>
            </div>
            <div>
                <span class="label">التخصص</span>
                <span class="val">{{ $insuranceClaim->doctor->specialty ?? 'الطب العام' }}</span>
            </div>
            <div>
                <span class="label">رقم الفاتورة المرجعية</span>
                <span class="val" style="font-family: monospace;">{{ $insuranceClaim->invoice->invoice_number ?? '—' }}</span>
            </div>
        </div>

        @if($insuranceClaim->notes)
        <div style="margin-top: 10px; font-size: 11px; background: #f9fafb; padding: 8px 12px; border-radius: 6px; border: 1px solid #e5e7eb;">
            <strong>التشخيص / ملاحظات التغطية:</strong> {{ $insuranceClaim->notes }}
        </div>
        @endif

        <!-- 3. Financial Calculation -->
        <div class="section-title">3. التفاصيل المالية ونسب التحمل (Claim Financials):</div>
        <table class="claim-table">
            <thead>
                <tr>
                    <th>البيان</th>
                    <th style="width: 25%; text-align: center;">المبلغ الإجمالي</th>
                    <th style="width: 25%; text-align: center;">تحمل المريض (Deductible)</th>
                    <th style="width: 25%; text-align: center;">مطالبة شركة التأمين</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>خدمات واستشارات طبية معتمدة</strong></td>
                    <td style="text-align: center; font-family: monospace; font-weight: 700;">{{ number_format($insuranceClaim->total_amount, 2) }} ر.س</td>
                    <td style="text-align: center; font-family: monospace; font-weight: 700; color: #b45309;">{{ number_format($insuranceClaim->patient_copay, 2) }} ر.س</td>
                    <td style="text-align: center; font-family: monospace; font-weight: 900; color: #0f766e;">{{ number_format($insuranceClaim->insurance_share, 2) }} ر.س</td>
                </tr>
            </tbody>
        </table>

        <!-- Signatures -->
        <div class="footer-signatures">
            <div class="sig-block">
                <div class="sig-line"></div>
                <p>إقرار وتوقيع المريض / المستفيد</p>
                <p style="font-size: 9px; color: #9ca3af;">أقر باستلامي للخدمة الطبية الموضحة أعلاه</p>
            </div>
            <div class="sig-block">
                <div class="sig-line"></div>
                <p>توقيع وختم الطبيب المعالج</p>
                <p style="font-size: 9px; color: #9ca3af;">د. {{ $insuranceClaim->doctor->name ?? 'Physician' }}</p>
            </div>
            <div class="sig-block">
                <div class="sig-line"></div>
                <p>ختم واعتماد قسم التأمين الطبي</p>
                <p style="font-size: 9px; color: #9ca3af;">{{ $settings['clinic_name'] ?? 'Medical Provider' }}</p>
            </div>
        </div>

        <div style="margin-top: 25px; text-align: center; font-size: 9px; color: #9ca3af; border-top: 1px solid #f3f4f6; padding-top: 8px;">
            صادرة إلكترونياً من نظام Clinc الطبي - نموذج مطالبة معتمد رسمي.
        </div>
    </div>

</body>
</html>
