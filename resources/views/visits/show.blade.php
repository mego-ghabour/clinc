@extends('layouts.app')

@section('title', 'ملف الزيارة الطبية - ' . $visit->patient->name)
@section('header', 'الملف الطبي للزيارة (EMR) - العيادات الرقمية')

@section('content')
<div class="space-y-6" x-data="{ showRadiologyModal: false }">

    <!-- EMR Header (Patient & Visit Meta) -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden relative">
        <div class="absolute top-0 left-0 w-1.5 h-full {{ $visit->status == 'مكتملة' ? 'bg-green-500' : ($visit->status == 'قيد الكشف' ? 'bg-blue-500' : 'bg-orange-500') }}"></div>
        
        <div class="p-6 md:p-8 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
            <!-- Patient Info -->
            <div class="flex items-center gap-5">
                <div class="w-16 h-16 rounded-2xl bg-teal-50 border border-teal-100 flex items-center justify-center text-teal-600 text-3xl shadow-sm">
                    @if($visit->patient->avatar)
                        <img src="{{ asset('storage/' . $visit->patient->avatar) }}" class="w-full h-full object-cover rounded-2xl">
                    @else
                        <i class="fa-solid fa-user-injured"></i>
                    @endif
                </div>
                <div>
                    <h1 class="text-2xl font-black text-gray-800 mb-1">
                        <a href="{{ route('patients.show', $visit->patient->id) }}" class="hover:text-teal-600 transition">{{ $visit->patient->name }}</a>
                    </h1>
                    <div class="flex flex-wrap items-center gap-3 text-sm text-gray-500 font-bold">
                        <span class="bg-gray-50 px-2 py-1 rounded-md border border-gray-100"><i class="fa-solid fa-file-medical text-gray-400"></i> MRN: <span class="font-mono text-gray-700">{{ $visit->patient->mrn }}</span></span>
                        <span><i class="fa-solid fa-cake-candles text-gray-400"></i> العمر: {{ \Carbon\Carbon::parse($visit->patient->date_of_birth)->age }} سنة</span>
                        <span><i class="fa-solid fa-venus-mars text-gray-400"></i> {{ $visit->patient->gender == 'M' || $visit->patient->gender == 'ذكر' ? 'ذكر' : 'أنثى' }}</span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto border-t lg:border-t-0 border-gray-100 pt-4 lg:pt-0 mt-2 lg:mt-0">
                @if($visit->status !== 'مكتملة')
                <button type="button" @click="showRadiologyModal = true" class="bg-indigo-50 text-indigo-600 hover:bg-indigo-100 border border-indigo-200 px-4 py-2.5 rounded-xl text-sm font-bold transition shadow-sm w-full lg:w-auto flex items-center justify-center gap-2">
                    <i class="fa-solid fa-x-ray"></i> طلب أشعة للمريض
                </button>
                <form action="{{ route('visits.update', $visit->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="مكتملة">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2.5 rounded-xl text-sm font-bold shadow-sm transition flex items-center justify-center gap-2 w-full lg:w-auto" onclick="return confirm('تأكيد إنهاء هذه الزيارة؟ سيتم إغلاق الملف الطبي ولن يعود قابلاً للتعديل.')">
                        <i class="fa-solid fa-check-double"></i> إنهاء وحفظ الزيارة
                    </button>
                </form>
                @endif
                <button onclick="window.print()" class="bg-gray-50 text-gray-700 hover:bg-gray-100 border border-gray-200 px-4 py-2.5 rounded-xl text-sm font-bold transition shadow-sm w-full lg:w-auto flex items-center justify-center gap-2 print:hidden">
                    <i class="fa-solid fa-print"></i> طباعة الوصفة
                </button>
            </div>
        </div>
    </div>

    <!-- Radiology Requests History for this Visit -->
    @php
        $radiologyRequests = \App\Models\RadiologyRequest::where('visit_id', $visit->id)->get();
    @endphp
    @if($radiologyRequests->count() > 0)
    <div class="bg-white rounded-3xl shadow-sm border border-indigo-100 overflow-hidden">
        <div class="bg-indigo-50/50 border-b border-indigo-100 px-6 py-3 flex justify-between items-center">
            <h3 class="font-black text-indigo-800 flex items-center gap-2 text-sm">
                <i class="fa-solid fa-x-ray text-indigo-500"></i> طلبات الأشعة لهذه الزيارة
            </h3>
        </div>
        <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($radiologyRequests as $rad)
                <div class="border {{ $rad->status == 'completed' ? 'border-green-200 bg-green-50' : 'border-orange-200 bg-orange-50' }} rounded-xl p-4 flex justify-between items-center">
                    <div>
                        <h4 class="font-bold text-gray-800 text-sm">{{ $rad->type }} ({{ $rad->body_part }})</h4>
                        @if($rad->status == 'pending')
                            <span class="text-[10px] font-black text-orange-600 mt-1 inline-block"><i class="fa-solid fa-clock"></i> في الانتظار بقسم الأشعة</span>
                        @else
                            <span class="text-[10px] font-black text-green-600 mt-1 inline-block"><i class="fa-solid fa-check-double"></i> التقرير جاهز</span>
                        @endif
                    </div>
                    @if($rad->status == 'completed')
                        <a href="{{ route('radiology.show', $rad->id) }}" target="_blank" class="bg-white border border-green-200 text-green-700 hover:bg-green-100 px-3 py-1.5 rounded-lg text-xs font-bold transition shadow-sm">
                            عرض النتيجة
                        </a>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Main EMR Workspace -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        
        <!-- Left Sidebar: Triage & Vitals -->
        <div class="lg:col-span-1 space-y-6">
            
            <!-- Vitals Form -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-black text-gray-800 mb-4 flex items-center gap-2 border-b border-gray-50 pb-2">
                    <i class="fa-solid fa-heart-pulse text-rose-500"></i> العلامات الحيوية (Triage)
                </h3>
                
                <form action="{{ route('visits.update', $visit->id) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-100">
                        <div class="w-8 h-8 rounded-full bg-white text-rose-500 flex items-center justify-center shadow-sm shrink-0"><i class="fa-solid fa-droplet"></i></div>
                        <div class="flex-1">
                            <label class="text-[10px] font-bold text-gray-500 uppercase">ضغط الدم (BP)</label>
                            <input type="text" name="blood_pressure" value="{{ $visit->blood_pressure }}" placeholder="120/80 mmHg" dir="ltr" class="w-full bg-transparent border-none p-0 text-sm font-bold text-gray-800 focus:ring-0">
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-100">
                        <div class="w-8 h-8 rounded-full bg-white text-red-500 flex items-center justify-center shadow-sm shrink-0"><i class="fa-solid fa-heart-circle-bolt"></i></div>
                        <div class="flex-1">
                            <label class="text-[10px] font-bold text-gray-500 uppercase">نبض القلب (HR)</label>
                            <input type="text" name="heart_rate" value="{{ $visit->heart_rate }}" placeholder="75 bpm" dir="ltr" class="w-full bg-transparent border-none p-0 text-sm font-bold text-gray-800 focus:ring-0">
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-100">
                        <div class="w-8 h-8 rounded-full bg-white text-orange-500 flex items-center justify-center shadow-sm shrink-0"><i class="fa-solid fa-temperature-half"></i></div>
                        <div class="flex-1">
                            <label class="text-[10px] font-bold text-gray-500 uppercase">درجة الحرارة (Temp)</label>
                            <input type="text" name="temperature" value="{{ $visit->temperature }}" placeholder="37.0 °C" dir="ltr" class="w-full bg-transparent border-none p-0 text-sm font-bold text-gray-800 focus:ring-0">
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-100">
                        <div class="w-8 h-8 rounded-full bg-white text-blue-500 flex items-center justify-center shadow-sm shrink-0"><i class="fa-solid fa-weight-scale"></i></div>
                        <div class="flex-1">
                            <label class="text-[10px] font-bold text-gray-500 uppercase">الوزن (Weight)</label>
                            <input type="text" name="weight" value="{{ $visit->weight }}" placeholder="70 kg" dir="ltr" class="w-full bg-transparent border-none p-0 text-sm font-bold text-gray-800 focus:ring-0">
                        </div>
                    </div>

                    @if($visit->status !== 'مكتملة')
                        <button type="submit" class="w-full bg-rose-50 text-rose-600 hover:bg-rose-100 border border-rose-200 py-2 rounded-xl text-xs font-bold transition shadow-sm mt-2">
                            <i class="fa-solid fa-save"></i> حفظ العلامات الحيوية
                        </button>
                    @endif
                </form>
            </div>
        </div>

        <!-- Right Main: Medical Record (Doctor) -->
        <div class="lg:col-span-3">
            <form action="{{ route('visits.update', $visit->id) }}" method="POST" class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-full">
                @csrf
                @method('PUT')
                
                <div class="bg-teal-50/50 border-b border-teal-100 px-6 py-4 flex justify-between items-center">
                    <h3 class="font-black text-teal-800 flex items-center gap-2 text-lg">
                        <i class="fa-solid fa-user-md text-teal-500"></i> التقرير الطبي والتشخيص (EMR)
                    </h3>
                    @if($visit->status !== 'مكتملة')
                        <button type="submit" class="bg-teal-custom hover:bg-teal-700 text-white px-5 py-2 rounded-xl text-sm font-bold shadow-sm transition">
                            <i class="fa-solid fa-save mr-1"></i> حفظ مؤقت للملف
                        </button>
                    @endif
                </div>

                <div class="p-6 space-y-6 flex-1">
                    
                    <!-- Chief Complaint -->
                    <div>
                        <label class="flex items-center gap-2 text-sm font-black text-gray-700 mb-2 uppercase">
                            <span class="w-6 h-6 rounded bg-red-50 text-red-500 flex items-center justify-center text-xs"><i class="fa-solid fa-comment-medical"></i></span>
                            الشكوى الرئيسية والأعراض (Chief Complaint)
                        </label>
                        <textarea name="chief_complaint" rows="3" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:border-teal-custom focus:bg-white rounded-2xl text-sm transition font-medium resize-none shadow-inner" placeholder="ما هي الشكوى الرئيسية التي يعاني منها المريض؟..." {{ $visit->status == 'مكتملة' ? 'readonly' : '' }}>{{ $visit->chief_complaint }}</textarea>
                    </div>

                    <!-- Diagnosis -->
                    <div>
                        <label class="flex items-center gap-2 text-sm font-black text-gray-700 mb-2 uppercase">
                            <span class="w-6 h-6 rounded bg-blue-50 text-blue-500 flex items-center justify-center text-xs"><i class="fa-solid fa-microscope"></i></span>
                            التشخيص الطبي النهائي (Diagnosis)
                        </label>
                        <textarea name="diagnosis" rows="3" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:border-teal-custom focus:bg-white rounded-2xl text-sm transition font-medium resize-none shadow-inner" placeholder="اكتب التشخيص الطبي النهائي أو المبدئي للحالة..." {{ $visit->status == 'مكتملة' ? 'readonly' : '' }}>{{ $visit->diagnosis }}</textarea>
                    </div>

                    <!-- Treatment Plan -->
                    <div>
                        <label class="flex items-center gap-2 text-sm font-black text-gray-700 mb-2 uppercase">
                            <span class="w-6 h-6 rounded bg-green-50 text-green-500 flex items-center justify-center text-xs"><i class="fa-solid fa-pills"></i></span>
                            خطة العلاج والوصفة الطبية (Treatment Plan / Rx)
                        </label>
                        <textarea name="treatment_plan" rows="5" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:border-teal-custom focus:bg-white rounded-2xl text-sm transition font-medium resize-none shadow-inner font-mono leading-relaxed" placeholder="اكتب الأدوية، الفحوصات المطلوبة، التعليمات الطبية..." {{ $visit->status == 'مكتملة' ? 'readonly' : '' }}>{{ $visit->treatment_plan }}</textarea>
                    </div>

                    <!-- Private Notes -->
                    <div>
                        <label class="flex items-center gap-2 text-sm font-black text-gray-700 mb-2 uppercase">
                            <span class="w-6 h-6 rounded bg-yellow-50 text-yellow-600 flex items-center justify-center text-xs"><i class="fa-solid fa-lock"></i></span>
                            ملاحظات خاصة بالطبيب (مخفية عن المريض)
                        </label>
                        <textarea name="doctor_notes" rows="2" class="w-full px-4 py-3 bg-yellow-50/30 border border-yellow-200 focus:border-yellow-400 focus:bg-yellow-50 rounded-2xl text-sm transition font-medium resize-none shadow-inner" placeholder="ملاحظات طبية، تفاصيل متابعة قادمة، إلخ..." {{ $visit->status == 'مكتملة' ? 'readonly' : '' }}>{{ $visit->doctor_notes }}</textarea>
                    </div>

                </div>
            </form>
        </div>

    </div>

    <!-- Request Radiology Modal -->
    <div x-show="showRadiologyModal" class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-gray-900/50 backdrop-blur-sm p-4" style="display: none;">
        <div @click.away="showRadiologyModal = false" class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden relative transform transition-all">
            <div class="bg-indigo-600 p-6 text-white flex justify-between items-center">
                <h3 class="font-black text-lg"><i class="fa-solid fa-x-ray ml-2"></i> إصدار طلب أشعة</h3>
                <button @click="showRadiologyModal = false" class="text-white/70 hover:text-white transition"><i class="fa-solid fa-xmark text-xl"></i></button>
            </div>
            <form action="{{ route('radiology.store') }}" method="POST" class="p-6">
                @csrf
                <input type="hidden" name="patient_id" value="{{ $visit->patient_id }}">
                <input type="hidden" name="doctor_id" value="{{ auth()->id() ?? $visit->doctor_id }}">
                <input type="hidden" name="visit_id" value="{{ $visit->id }}">
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">نوع الأشعة المطلوب <span class="text-red-500">*</span></label>
                        <select name="type" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 focus:border-indigo-500 rounded-xl text-sm font-bold transition">
                            <option value="">-- اختر النوع --</option>
                            <option value="X-Ray (أشعة سينية)">X-Ray (أشعة سينية)</option>
                            <option value="MRI (رنين مغناطيسي)">MRI (رنين مغناطيسي)</option>
                            <option value="CT Scan (أشعة مقطعية)">CT Scan (أشعة مقطعية)</option>
                            <option value="Ultrasound (موجات فوق صوتية)">Ultrasound (موجات فوق صوتية)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">الجزء المراد تصويره <span class="text-red-500">*</span></label>
                        <input type="text" name="body_part" required placeholder="مثال: الصدر، الركبة اليمنى، الرأس..." class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 focus:border-indigo-500 rounded-xl text-sm font-bold transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">ملاحظات سريرية لفني الأشعة</label>
                        <textarea name="clinical_notes" rows="3" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 focus:border-indigo-500 rounded-xl text-sm transition font-medium resize-none" placeholder="اشرح الغرض من الطلب أو ما تبحث عنه..."></textarea>
                    </div>
                </div>
                <div class="mt-6 flex gap-3">
                    <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-3 rounded-xl text-sm font-bold shadow-sm transition">إرسال الطلب لقسم الأشعة</button>
                    <button type="button" @click="showRadiologyModal = false" class="px-6 bg-gray-100 hover:bg-gray-200 text-gray-700 py-3 rounded-xl text-sm font-bold transition">إلغاء</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
