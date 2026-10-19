<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>وصفة طبية - {{ $prescription->prescription_number }} - {{ $prescription->patient->name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        * { box-sizing: border-box; font-family: 'Cairo', sans-serif; }
        body { background-color: #f8fafc; color: #1e293b; margin: 0; padding: 20px; }
        .rx-card {
            background: #fff;
            max-width: 750px;
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
        .rx-badge { text-align: left; }
        .rx-symbol { font-size: 32px; font-weight: 900; color: #0d9488; font-family: serif; font-style: italic; }
        .patient-box {
            background: #f0fdfa;
            border: 1px solid #ccfbf1;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 25px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            font-size: 11px;
        }
        .patient-box .label { color: #64748b; font-weight: 700; font-size: 10px; display: block; }
        .patient-box .val { font-weight: 700; color: #0f172a; }
        .med-item {
            border-bottom: 1px dashed #e2e8f0;
            padding: 14px 0;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .med-title { font-size: 14px; font-weight: 900; color: #0f172a; }
        .med-instructions { font-size: 12px; color: #0d9488; font-weight: 700; margin-top: 4px; }
        .med-qty { font-family: monospace; font-size: 13px; font-weight: 900; color: #475569; }
        .footer-signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 45px;
            padding-top: 20px;
            border-top: 1px solid #cbd5e1;
        }
        .sig-block { text-align: center; width: 180px; }
        .sig-block p { margin: 4px 0 0 0; font-size: 11px; color: #64748b; font-weight: 700; }
        .sig-line { height: 35px; border-bottom: 1px solid #94a3b8; margin-bottom: 4px; }
        .no-print-bar {
            max-width: 750px;
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
            .rx-card { box-shadow: none; border: none; padding: 20px; max-width: 100%; }
        }
    </style>
</head>
<body>

    <div class="no-print-bar">
        <a href="{{ route('prescriptions.show', $prescription->id) }}" style="text-decoration: none; color: #475569; font-size: 13px; font-weight: 700;">
            <i class="fa-solid fa-arrow-right"></i> العودة للوصفة
        </a>
        <button onclick="window.print()" class="print-btn">
            <i class="fa-solid fa-print"></i> طباعة الوصفة الطبية
        </button>
    </div>

    <div class="rx-card">
        <!-- Header -->
        <div class="header">
            <div class="clinic-info">
                <h1>{{ $settings['clinic_name'] ?? 'مجمع العيادات الطبية' }}</h1>
                <p>قسم العيادات الخارجية والصيدلية السريرية</p>
                <p>هاتف: {{ $settings['clinic_phone'] ?? '—' }} | العنوان: {{ $settings['clinic_address'] ?? '—' }}</p>
            </div>
            <div class="rx-badge">
                <div class="rx-symbol">℞</div>
                <div style="font-family: monospace; font-size: 12px; font-weight: 700; color: #475569;">{{ $prescription->prescription_number }}</div>
                <div style="font-size: 10px; color: #64748b;">{{ now()->format('Y-m-d') }}</div>
            </div>
        </div>

        <!-- Patient Info -->
        <div class="patient-box">
            <div>
                <span class="label">اسم المريض</span>
                <span class="val">{{ $prescription->patient->name }}</span>
            </div>
            <div>
                <span class="label">رقم الملف (MRN)</span>
                <span class="val" style="font-family: monospace;">{{ $prescription->patient->mrn }}</span>
            </div>
            <div>
                <span class="label">العمر / الجنس</span>
                <span class="val">{{ \Carbon\Carbon::parse($prescription->patient->date_of_birth)->age }} سنة / {{ $prescription->patient->gender == 'M' || $prescription->patient->gender == 'ذكر' ? 'ذكر' : 'أنثى' }}</span>
            </div>
            <div>
                <span class="label">الطبيب المعالج</span>
                <span class="val">د. {{ $prescription->doctor->name ?? 'طبيب عام' }}</span>
            </div>
        </div>

        @if($prescription->patient->allergies)
        <div style="margin-bottom: 15px; font-size: 11px; background: #fff1f2; padding: 8px 12px; border-radius: 6px; border-right: 3px solid #e11d48; color: #be123c;">
            <strong>تنبيه حساسية المريض:</strong> {{ $prescription->patient->allergies }}
        </div>
        @endif

        <!-- Medications List -->
        <div style="margin-top: 20px;">
            <div style="font-size: 12px; font-weight: 900; color: #0f766e; border-bottom: 2px solid #ccfbf1; padding-bottom: 6px; margin-bottom: 10px;">
                قائمة الأدوية الموصوفة والتعليمات:
            </div>

            @foreach($prescription->items as $index => $item)
            <div class="med-item">
                <div>
                    <div class="med-title">{{ $index + 1 }}. {{ $item->medicine_name }}</div>
                    <div class="med-instructions">
                        <i class="fa-regular fa-clock" style="margin-left: 4px;"></i>
                        {{ $item->dosage }} • {{ $item->frequency }} • لمدة {{ $item->duration }}
                    </div>
                    @if($item->instructions)
                        <div style="font-size: 11px; color: #64748b; margin-top: 2px;">ملاحظة: {{ $item->instructions }}</div>
                    @endif
                </div>
                <div class="med-qty">
                    الكمية: {{ $item->quantity }}
                </div>
            </div>
            @endforeach
        </div>

        @if($prescription->notes)
        <div style="margin-top: 20px; font-size: 11px; background: #f8fafc; padding: 10px 14px; border-radius: 6px; border-right: 3px solid #0d9488;">
            <strong>تعليمات عامة:</strong> {{ $prescription->notes }}
        </div>
        @endif

        <!-- Footer Signatures -->
        <div class="footer-signatures">
            <div class="sig-block">
                <div class="sig-line"></div>
                <p>توقيع وختم الطبيب المعالج</p>
                <p style="font-size: 10px; color: #94a3b8;">د. {{ $prescription->doctor->name ?? 'Doctor' }}</p>
            </div>
            <div class="sig-block">
                <div class="sig-line"></div>
                <p>ختم صيدلية العيادة</p>
                <p style="font-size: 10px; color: #94a3b8;">{{ $prescription->pharmacist->name ?? 'Clinical Pharmacy' }}</p>
            </div>
        </div>

        <div style="margin-top: 25px; text-align: center; font-size: 9px; color: #94a3b8; border-top: 1px solid #f1f5f9; padding-top: 8px;">
            هذه الوصفة الطبية صادرة ومعتمدة إلكترونياً من نظام Clinc الطبي.
        </div>
    </div>

</body>
</html>
