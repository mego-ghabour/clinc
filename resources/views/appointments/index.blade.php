@extends('layouts.app')

@section('title', 'جدول المواعيد - Clinc')
@section('header', 'إدارة المواعيد والحجوزات')

@section('content')
<div x-data="appointmentManager()">
    
    <!-- Header Actions -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 mb-1">جدول المواعيد</h1>
            <p class="text-gray-500 text-sm">إدارة حجوزات المرضى، تنظيم العيادات، وتأكيد المواعيد</p>
        </div>
        <button @click="openModal()" class="bg-teal-custom hover:bg-teal-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold transition shadow-sm flex items-center gap-2">
            <i class="fa-solid fa-calendar-plus"></i> حجز موعد جديد
        </button>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6">
        <form action="{{ route('appointments.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-center">
            
            <div class="flex-1 w-full relative">
                <i class="fa-solid fa-search absolute right-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث باسم المريض أو رقم الموعد..." class="w-full pr-11 pl-4 py-2.5 bg-gray-50 border border-transparent focus:border-teal-custom/50 focus:bg-white rounded-xl text-sm transition font-bold">
            </div>

            <div class="w-full md:w-48">
                <input type="date" name="date" value="{{ request('date', (!request('search') && !request('all_dates') ? date('Y-m-d') : '')) }}" class="w-full px-4 py-2.5 bg-gray-50 border border-transparent focus:border-teal-custom/50 focus:bg-white rounded-xl text-sm transition font-bold text-gray-700" onchange="this.form.submit()">
            </div>
            
            <div class="w-full md:w-56">
                <select name="doctor_id" class="w-full px-4 py-2.5 bg-gray-50 border border-transparent focus:border-teal-custom/50 focus:bg-white rounded-xl text-sm transition font-bold text-gray-700" onchange="this.form.submit()">
                    <option value="">كل الأطباء</option>
                    @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}" {{ request('doctor_id') == $doctor->id ? 'selected' : '' }}>د. {{ $doctor->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="w-full md:w-48">
                <select name="status" class="w-full px-4 py-2.5 bg-gray-50 border border-transparent focus:border-teal-custom/50 focus:bg-white rounded-xl text-sm transition font-bold text-gray-700" onchange="this.form.submit()">
                    <option value="all">كل الحالات</option>
                    <option value="مجدول" {{ request('status') == 'مجدول' ? 'selected' : '' }}>مجدول (قادم)</option>
                    <option value="مؤكد" {{ request('status') == 'مؤكد' ? 'selected' : '' }}>مؤكد (تم التأكيد)</option>
                    <option value="تم الحضور" {{ request('status') == 'تم الحضور' ? 'selected' : '' }}>تم الحضور</option>
                    <option value="ملغي" {{ request('status') == 'ملغي' ? 'selected' : '' }}>ملغي</option>
                </select>
            </div>
            
            @if(request()->hasAny(['search', 'date', 'doctor_id', 'status']))
                <a href="{{ route('appointments.index', ['all_dates' => '1']) }}" class="text-red-500 bg-red-50 hover:bg-red-100 px-4 py-2.5 rounded-xl text-sm font-bold transition flex items-center gap-2">
                    <i class="fa-solid fa-times"></i> مسح
                </a>
            @endif
        </form>
    </div>

    <!-- Appointments List -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden mb-8">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead class="bg-gray-50 text-gray-600 font-bold border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">رقم الموعد / الوقت</th>
                        <th class="px-6 py-4">المريض</th>
                        <th class="px-6 py-4">الطبيب والخدمة</th>
                        <th class="px-6 py-4">تاريخ الموعد</th>
                        <th class="px-6 py-4">حالة الموعد</th>
                        <th class="px-6 py-4 text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-gray-700">
                    @forelse($appointments as $appointment)
                    <tr class="hover:bg-gray-50/50 transition group">
                        <td class="px-6 py-4">
                            <div class="font-mono font-bold text-teal-600 mb-0.5">{{ $appointment->appointment_number }}</div>
                            <div class="text-sm font-black text-gray-800" dir="ltr">
                                <i class="fa-regular fa-clock text-gray-400 mr-1"></i> 
                                {{ $appointment->appointment_time ? \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') : 'غير محدد' }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('patients.show', $appointment->patient_id) }}" class="font-black text-gray-800 text-sm hover:text-teal-600 transition">{{ $appointment->patient->name }}</a>
                            <div class="text-xs text-gray-500 font-mono mt-0.5"><i class="fa-solid fa-mobile-screen text-gray-400"></i> {{ $appointment->patient->mobile ?? 'لا يوجد' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-teal-700 text-sm">د. {{ $appointment->doctor->name }}</div>
                            <div class="text-xs text-gray-500 font-bold mt-0.5 bg-gray-100 inline-block px-2 py-0.5 rounded">{{ $appointment->service }}</div>
                        </td>
                        <td class="px-6 py-4 font-bold text-gray-700 font-mono" dir="ltr">
                            {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('Y-m-d') }}
                            @if(\Carbon\Carbon::parse($appointment->appointment_date)->isToday())
                                <span class="ml-2 bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-[10px]">اليوم</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusColors = [
                                    'مجدول' => 'bg-yellow-50 text-yellow-600 border-yellow-100',
                                    'مؤكد' => 'bg-blue-50 text-blue-600 border-blue-100',
                                    'تم الحضور' => 'bg-green-50 text-green-600 border-green-100',
                                    'ملغي' => 'bg-red-50 text-red-600 border-red-100',
                                ];
                                $color = $statusColors[$appointment->status] ?? 'bg-gray-50 text-gray-600 border-gray-100';
                            @endphp
                            <span class="inline-flex px-3 py-1.5 rounded-full text-xs font-black border {{ $color }}">
                                {{ $appointment->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            
                            <!-- Quick Action Dropdown for Status -->
                            <div class="flex items-center justify-center gap-2" x-data="{ open: false }">
                                <div class="relative">
                                    <button @click="open = !open" @click.away="open = false" class="bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-teal-600 px-3 py-2 rounded-lg text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
                                        تغيير الحالة <i class="fa-solid fa-chevron-down text-[10px]"></i>
                                    </button>
                                    
                                    <div x-show="open" class="absolute left-0 mt-2 w-40 bg-white rounded-xl shadow-lg border border-gray-100 z-10 py-2 overflow-hidden" style="display: none;">
                                        @foreach(['مجدول', 'مؤكد', 'تم الحضور', 'ملغي'] as $stat)
                                            @if($appointment->status !== $stat)
                                                <form action="{{ route('appointments.update', $appointment->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="{{ $stat }}">
                                                    <button type="submit" class="w-full text-right px-4 py-2 text-sm font-bold text-gray-700 hover:bg-gray-50 hover:text-teal-600 transition">
                                                        {{ $stat }}
                                                    </button>
                                                </form>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>

                                <!-- WhatsApp Actions -->
                                <form action="{{ route('appointments.whatsapp', $appointment->id) }}" method="POST" class="inline" title="إرسال تذكير واتساب آلي عبر بوابة النظام">
                                    @csrf
                                    <input type="hidden" name="type" value="reminder">
                                    <button type="submit" class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white flex items-center justify-center transition shadow-sm" title="إرسال تذكير واتساب آلي">
                                        <i class="fa-brands fa-whatsapp text-sm"></i>
                                    </button>
                                </form>

                                @php
                                    $patientPhone = preg_replace('/[^0-9]/', '', $appointment->patient->mobile ?? '');
                                    if (str_starts_with($patientPhone, '05')) { $patientPhone = '966' . substr($patientPhone, 1); }
                                    $waText = urlencode("مرحباً عزيزنا {$appointment->patient->first_name}، نود تذكيركم بموعدكم لدى د. {$appointment->doctor->name} بتاريخ {$appointment->appointment_date} في تمام الساعة " . ($appointment->appointment_time ? \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') : ''));
                                @endphp
                                @if($patientPhone)
                                    <a href="https://wa.me/{{ $patientPhone }}?text={{ $waText }}" target="_blank" class="w-8 h-8 rounded-lg bg-teal-50 text-teal-700 hover:bg-teal-600 hover:text-white flex items-center justify-center transition shadow-sm" title="فتح شات الواتساب مباشرة">
                                        <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                                    </a>
                                @endif
                                
                                <form action="{{ route('appointments.destroy', $appointment->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الموعد نهائياً؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white flex items-center justify-center transition shadow-sm">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>

                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center text-gray-500">
                            <i class="fa-regular fa-calendar-xmark text-5xl mb-4 text-gray-300"></i>
                            <p class="font-bold text-lg text-gray-700">لا توجد مواعيد</p>
                            <p class="text-sm">لم يتم العثور على أي مواعيد مطابقة لبحثك في هذا اليوم.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($appointments->hasPages())
        <div class="flex justify-center mb-8">
            {{ $appointments->links() }}
        </div>
    @endif

    <!-- Add Appointment Modal -->
    <div x-show="isModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="isModalOpen" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm transition-opacity" @click="isModalOpen = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="isModalOpen" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 class="inline-block align-bottom bg-white rounded-3xl text-right overflow-visible shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full border border-gray-100">
                
                <form action="{{ route('appointments.store') }}" method="POST">
                    @csrf
                    
                    <div class="bg-white px-6 py-5 border-b border-gray-100 flex justify-between items-center rounded-t-3xl sticky top-0 z-10">
                        <h3 class="text-xl font-black text-gray-800 flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-teal-50 text-teal-600 flex items-center justify-center text-sm shadow-sm">
                                <i class="fa-solid fa-calendar-plus"></i>
                            </div>
                            حجز موعد جديد
                        </h3>
                        <button type="button" @click="isModalOpen = false" class="w-8 h-8 rounded-full bg-gray-50 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition flex items-center justify-center">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <div class="px-8 py-6 space-y-6 max-h-[70vh] overflow-y-auto custom-scrollbar">
                        
                        <div class="bg-gray-50 p-5 rounded-2xl border border-gray-100">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">المريض (صاحب الحجز) <span class="text-red-500">*</span></label>
                                    <select name="patient_id" required class="w-full px-4 py-3 bg-white border border-gray-200 focus:border-teal-custom rounded-xl text-sm transition font-bold shadow-sm">
                                        <option value="" disabled selected>ابحث وافتر المرضى المسجلين...</option>
                                        @foreach($patients as $patient)
                                            <option value="{{ $patient->id }}">{{ $patient->name }} ({{ $patient->mrn }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">الطبيب المعالج <span class="text-red-500">*</span></label>
                                    <select name="doctor_id" required class="w-full px-4 py-3 bg-white border border-gray-200 focus:border-teal-custom rounded-xl text-sm transition font-bold shadow-sm">
                                        <option value="" disabled selected>اختر الطبيب المتاح...</option>
                                        @foreach($doctors as $doctor)
                                            <option value="{{ $doctor->id }}">د. {{ $doctor->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 p-5 rounded-2xl border border-gray-100">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">تاريخ الموعد <span class="text-red-500">*</span></label>
                                    <input type="date" name="appointment_date" required class="w-full px-4 py-3 bg-white border border-gray-200 focus:border-teal-custom rounded-xl text-sm transition font-bold shadow-sm text-right">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">وقت الموعد <span class="text-red-500">*</span></label>
                                    <input type="time" name="appointment_time" required class="w-full px-4 py-3 bg-white border border-gray-200 focus:border-teal-custom rounded-xl text-sm transition font-bold shadow-sm text-right" dir="ltr">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">الخدمة المطلوبة <span class="text-red-500">*</span></label>
                                <select name="service" required class="w-full px-4 py-3 bg-white border border-gray-200 focus:border-teal-custom rounded-xl text-sm transition font-bold shadow-sm">
                                    <option value="كشفية جديدة">كشفية جديدة</option>
                                    <option value="استشارة طبيب">استشارة طبيب (مراجعة)</option>
                                    <option value="متابعة حمل">متابعة حمل</option>
                                    <option value="تنظيف أسنان">تنظيف أسنان</option>
                                    <option value="جلسة ليزر">جلسة ليزر</option>
                                    <option value="أخرى">أخرى</option>
                                </select>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">ملاحظات الحجز (اختياري)</label>
                            <textarea name="notes" rows="2" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:border-teal-custom focus:bg-white rounded-xl text-sm transition font-bold resize-none" placeholder="أي ملاحظات إضافية بخصوص هذا الموعد..."></textarea>
                        </div>

                    </div>

                    <div class="bg-white px-6 py-4 border-t border-gray-100 flex justify-end gap-3 rounded-b-3xl">
                        <button type="button" @click="isModalOpen = false" class="bg-gray-50 border border-gray-200 text-gray-700 hover:bg-gray-100 px-6 py-2.5 rounded-xl text-sm font-bold transition shadow-sm">
                            إلغاء
                        </button>
                        <button type="submit" class="bg-teal-custom hover:bg-teal-700 text-white px-8 py-2.5 rounded-xl text-sm font-bold shadow-sm transition flex items-center gap-2">
                            <i class="fa-solid fa-calendar-check"></i> تأكيد وحفظ الموعد
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    window.appointmentManager = function() {
        return {
            isModalOpen: {{ request('open_modal') ? 'true' : 'false' }},
            openModal() {
                this.isModalOpen = true;
            }
        };
    };
</script>
@endsection
