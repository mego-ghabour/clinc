@extends('layouts.app')

@section('title', 'تفاصيل طلب الأشعة - Clinc')
@section('header', 'تفاصيل طلب الأشعة (Radiology Report)')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 flex justify-between items-center relative overflow-hidden">
        <div class="absolute top-0 left-0 w-24 h-24 bg-indigo-50 rounded-full -ml-12 -mt-12 opacity-50"></div>
        <div class="flex items-center gap-4 relative z-10">
            <div class="w-12 h-12 rounded-xl {{ $radiology->status == 'completed' ? 'bg-green-50 text-green-600 border-green-100' : 'bg-orange-50 text-orange-600 border-orange-100' }} flex items-center justify-center text-xl shadow-sm border">
                @if($radiology->status == 'completed')
                    <i class="fa-solid fa-check-double"></i>
                @else
                    <i class="fa-solid fa-clock"></i>
                @endif
            </div>
            <div>
                <h2 class="text-xl font-black text-gray-800 mb-1">طلب أشعة: {{ $radiology->type }} ({{ $radiology->body_part }})</h2>
                <p class="text-xs text-gray-500 font-bold">المريض: <a href="{{ route('patients.show', $radiology->patient_id) }}" class="text-indigo-600 hover:underline">{{ $radiology->patient->name }}</a> | MRN: {{ $radiology->patient->mrn }}</p>
            </div>
        </div>
        <a href="{{ route('radiology.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2.5 rounded-xl text-sm font-bold transition">
            العودة للطابور
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left: Clinical Info -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-black text-gray-800 mb-4 flex items-center gap-2 border-b border-gray-50 pb-2">
                    <i class="fa-solid fa-notes-medical text-indigo-500"></i> التفاصيل السريرية (من الطبيب)
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">الطبيب الطالب</p>
                        <p class="text-sm font-bold text-gray-800 bg-gray-50 p-2 rounded-lg">د. {{ $radiology->doctor->name }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">تاريخ الطلب</p>
                        <p class="text-sm font-bold text-gray-800 bg-gray-50 p-2 rounded-lg font-mono" dir="ltr">{{ $radiology->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-red-400 uppercase mb-1">ملاحظات سريرية (Clinical Notes)</p>
                        <div class="text-sm font-bold text-gray-700 bg-red-50 border border-red-100 p-3 rounded-xl leading-relaxed">
                            {{ $radiology->clinical_notes ?? 'لا توجد ملاحظات مرفقة من الطبيب.' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Upload & Report -->
        <div class="lg:col-span-2">
            @if($radiology->status == 'pending')
                <!-- Form for Radiologist to upload -->
                <form action="{{ route('radiology.update', $radiology->id) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-3xl shadow-sm border border-indigo-100 overflow-hidden">
                    @csrf
                    @method('PUT')
                    
                    <div class="bg-indigo-50/50 border-b border-indigo-100 p-6">
                        <h3 class="font-black text-indigo-800 flex items-center gap-2">
                            <i class="fa-solid fa-file-medical text-indigo-500"></i> إرفاق النتيجة والتقرير الطبي
                        </h3>
                    </div>

                    <div class="p-6 space-y-6">
                        
                        <!-- Upload Image -->
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">صورة الأشعة (X-Ray / MRI Image) <span class="text-red-500">*</span></label>
                            <div class="flex items-center justify-center w-full" x-data="imageUploader()">
                                <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-48 border-2 border-indigo-200 border-dashed rounded-2xl cursor-pointer bg-gray-50 hover:bg-indigo-50 transition overflow-hidden relative group">
                                    
                                    <!-- Preview Image -->
                                    <div x-show="previewUrl" style="display: none;" class="absolute inset-0 z-10 w-full h-full bg-black/80 flex items-center justify-center p-2">
                                        <img :src="previewUrl" class="max-h-full max-w-full object-contain rounded-lg">
                                        <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                                            <span class="text-white font-bold bg-black/50 px-4 py-2 rounded-lg backdrop-blur-sm">تغيير الصورة</span>
                                        </div>
                                    </div>

                                    <!-- Upload UI -->
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center px-4">
                                        <i class="fa-solid fa-cloud-arrow-up text-4xl text-indigo-400 mb-3"></i>
                                        <p class="mb-2 text-sm font-bold text-gray-700">اضغط لرفع الصورة، أو اسحب وأفلت هنا</p>
                                        <p class="text-xs text-gray-500 font-medium">يدعم صيغ: JPG, PNG, DICOM (أقل من 5MB)</p>
                                    </div>
                                    <input id="dropzone-file" type="file" name="image_file" class="hidden" accept="image/*" @change="updatePreview" />
                                </label>
                            </div>
                        </div>

                        <!-- Medical Report -->
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">التقرير الطبي (Radiology Report) <span class="text-red-500">*</span></label>
                            <textarea name="report_text" rows="6" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:border-indigo-500 focus:bg-white rounded-2xl text-sm transition font-medium resize-none shadow-inner" placeholder="اكتب التقرير الطبي المفصل هنا ليقرأه الطبيب المعالج..."></textarea>
                        </div>

                    </div>
                    
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-end">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-xl text-sm font-bold shadow-sm transition flex items-center gap-2">
                            <i class="fa-solid fa-check-double"></i> حفظ التقرير وإنهاء الطلب
                        </button>
                    </div>
                </form>
            @else
                <!-- View Completed Report -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-green-50/50 border-b border-green-100 p-6 flex justify-between items-center">
                        <h3 class="font-black text-green-800 flex items-center gap-2">
                            <i class="fa-solid fa-check-circle text-green-500"></i> التقرير الطبي مكتمل
                        </h3>
                        <button onclick="window.print()" class="text-green-700 bg-white border border-green-200 hover:bg-green-100 px-4 py-2 rounded-lg text-xs font-bold transition">
                            <i class="fa-solid fa-print"></i> طباعة التقرير
                        </button>
                    </div>

                    <div class="p-6 space-y-6">
                        @if($radiology->image_path)
                        <div>
                            <h4 class="text-xs font-bold text-gray-400 uppercase mb-2">صورة الأشعة المرفقة</h4>
                            <div class="rounded-2xl border border-gray-200 overflow-hidden bg-black flex justify-center items-center h-[400px]">
                                <img src="{{ asset('storage/' . $radiology->image_path) }}" class="max-h-full max-w-full object-contain">
                            </div>
                        </div>
                        @endif

                        <div>
                            <h4 class="text-xs font-bold text-gray-400 uppercase mb-2">التقرير الطبي (Radiology Report)</h4>
                            <div class="bg-gray-50 border border-gray-200 rounded-2xl p-5 text-sm font-bold text-gray-800 leading-loose whitespace-pre-line">
                                {{ $radiology->report_text }}
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center border-t border-gray-100 pt-4 mt-4 text-xs font-bold text-gray-500">
                            <div>فني الأشعة / الطبيب: د. {{ $radiology->radiologist->name ?? 'غير محدد' }}</div>
                            <div class="font-mono" dir="ltr">Completed at: {{ $radiology->completed_at ? $radiology->completed_at->format('Y-m-d H:i') : '' }}</div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

    </div>
</div>

<script>
    function imageUploader() {
        return {
            previewUrl: null,
            updatePreview(event) {
                const file = event.target.files[0];
                if (file) {
                    this.previewUrl = URL.createObjectURL(file);
                }
            }
        }
    }
</script>
@endsection
