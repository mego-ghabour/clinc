@extends('layouts.app')

@section('title', 'جدول عمل الأطباء - Clinc')
@section('header', 'مواعيد الأطباء والفترات الزمنية')

@section('content')
<div class="flex flex-col lg:flex-row gap-6">

    <!-- Right Panel: Doctors Sidebar -->
    <div class="w-full lg:w-72 shrink-0">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-sm font-black text-gray-800 mb-4 flex items-center gap-2 border-b border-gray-50 pb-2">
                <i class="fa-solid fa-user-doctor text-teal-500"></i> قائمة الأطباء
            </h3>
            
            <div class="space-y-2 h-[600px] overflow-y-auto pr-2 custom-scrollbar">
                @foreach($doctors as $doctor)
                @php
                    $isSelected = $selectedDoctor && $selectedDoctor->id === $doctor->id;
                @endphp
                <a href="{{ route('doctor-schedules.show', $doctor->id) }}" class="flex items-center justify-between p-3 rounded-xl transition border {{ $isSelected ? 'border-teal-custom bg-teal-50 shadow-sm' : 'border-transparent hover:bg-gray-50' }}">
                    <div class="text-right">
                        <h4 class="font-bold text-sm {{ $isSelected ? 'text-teal-700' : 'text-gray-800' }}">د. {{ $doctor->name }}</h4>
                        <p class="text-xs {{ $isSelected ? 'text-teal-600/70' : 'text-gray-500' }}">{{ $doctor->specialization ?? 'طبيب عام' }}</p>
                    </div>
                    <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center {{ $isSelected ? 'text-teal-600 border border-teal-200' : 'text-gray-400 border border-gray-200' }} text-xs font-bold shrink-0">
                        <i class="fa-solid fa-chevron-left"></i>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Left Panel: Doctor's Schedule Workspace -->
    <div class="flex-1 space-y-6">
        @if(!$selectedDoctor)
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-16 text-center h-[650px] flex flex-col justify-center items-center">
                <div class="w-24 h-24 bg-teal-50 text-teal-200 rounded-full flex items-center justify-center text-5xl mb-6 mx-auto">
                    <i class="fa-regular fa-clock"></i>
                </div>
                <h2 class="text-2xl font-black text-gray-700 mb-2">فترات عمل الأطباء</h2>
                <p class="text-gray-500 max-w-md mx-auto leading-relaxed text-sm font-bold">
                    الرجاء اختيار طبيب لتقسيم أيام العمل إلى فترات (صباحية ومسائية) وتحديد مدة الكشف.
                </p>
            </div>
        @else
            <!-- Selected Doctor Header -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 flex justify-between items-center relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-teal-50 rounded-full -mr-12 -mt-12 opacity-50"></div>
                
                <div class="flex items-center gap-5 relative z-10">
                    <div class="w-16 h-16 rounded-2xl bg-teal-50 border border-teal-100 flex items-center justify-center text-teal-600 text-2xl shadow-sm font-black overflow-hidden">
                        @if($selectedDoctor->avatar)
                            <img src="{{ asset('storage/' . $selectedDoctor->avatar) }}" class="w-full h-full object-cover">
                        @else
                            {{ mb_substr($selectedDoctor->name, 0, 1) }}
                        @endif
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-gray-800 mb-1">د. {{ $selectedDoctor->name }}</h2>
                        <div class="flex gap-2">
                            <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs font-bold">{{ $selectedDoctor->specialization ?? 'طبيب عام' }}</span>
                            @if($selectedDoctor->is_present)
                                <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold"><i class="fa-solid fa-check-circle"></i> متاح حالياً</span>
                            @else
                                <span class="bg-orange-100 text-orange-700 px-2 py-1 rounded text-xs font-bold"><i class="fa-solid fa-plane"></i> في إجازة</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Weekly Schedule Form -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gray-50 border-b border-gray-100 px-6 py-4">
                    <h3 class="font-black text-gray-800 flex items-center gap-2">
                        <i class="fa-solid fa-layer-group text-teal-500"></i> تقسيم الفترات ومدة الكشف
                    </h3>
                </div>
                
                <form action="{{ route('doctor-schedules.store') }}" method="POST" class="p-6">
                    @csrf
                    <input type="hidden" name="doctor_id" value="{{ $selectedDoctor->id }}">
                    
                    @php
                        $days = [
                            'Sunday' => 'الأحد',
                            'Monday' => 'الإثنين',
                            'Tuesday' => 'الثلاثاء',
                            'Wednesday' => 'الأربعاء',
                            'Thursday' => 'الخميس',
                            'Friday' => 'الجمعة',
                            'Saturday' => 'السبت',
                        ];
                    @endphp

                    <div class="grid grid-cols-1 gap-6 mb-8">
                        @foreach($days as $enDay => $arDay)
                            @php
                                $schedule = $schedules[$enDay] ?? null;
                                $isWorking = $schedule ? $schedule->is_working : ($enDay !== 'Friday' && $enDay !== 'Saturday');
                                $duration = $schedule ? $schedule->consultation_duration : 15;
                                $shifts = $schedule && $schedule->shifts ? json_encode($schedule->shifts) : '[{"start": "08:00", "end": "12:00"}]';
                            @endphp
                            
                            <div class="bg-white border {{ $isWorking ? 'border-teal-300 shadow-sm' : 'border-gray-200' }} rounded-2xl overflow-hidden transition-all" x-data="scheduleDay('{{ $enDay }}', {{ $isWorking ? 'true' : 'false' }}, {{ $duration }}, {{ $shifts }})">
                                
                                <!-- Day Header -->
                                <div class="px-5 py-3 flex items-center justify-between" :class="isWorking ? 'bg-teal-50/50' : 'bg-gray-50'">
                                    <div class="flex items-center gap-3">
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="schedules[{{ $enDay }}][is_working]" class="sr-only peer" x-model="isWorking" value="1">
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:right-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-teal-500"></div>
                                        </label>
                                        <span class="font-black text-lg" :class="isWorking ? 'text-teal-800' : 'text-gray-400'">{{ $arDay }}</span>
                                    </div>
                                    
                                    <div x-show="isWorking" class="flex items-center gap-2">
                                        <span class="text-xs font-bold text-teal-700">مدة الكشف (دقيقة):</span>
                                        <select :name="'schedules[' + dayName + '][consultation_duration]'" x-model="duration" class="border border-teal-200 bg-white rounded px-2 py-1 text-xs font-bold focus:border-teal-custom outline-none">
                                            <option value="10">10 دقائق</option>
                                            <option value="15">15 دقيقة</option>
                                            <option value="20">20 دقيقة</option>
                                            <option value="30">30 دقيقة (نصف ساعة)</option>
                                            <option value="45">45 دقيقة</option>
                                            <option value="60">60 دقيقة (ساعة)</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <!-- Shifts Container -->
                                <div class="p-5" x-show="isWorking" x-transition>
                                    <div class="flex flex-wrap gap-4">
                                        
                                        <template x-for="(shift, index) in shifts" :key="index">
                                            <div class="flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-xl p-2 relative group">
                                                <div class="flex flex-col gap-1">
                                                    <div class="flex items-center justify-between gap-2">
                                                        <span class="text-[10px] font-bold text-gray-500 w-6">من:</span>
                                                        <input type="time" :name="'schedules[' + dayName + '][shifts]['+index+'][start]'" x-model="shift.start" class="px-2 py-1 bg-white border border-gray-200 rounded text-xs font-bold text-gray-700 focus:border-teal-custom" dir="ltr" required>
                                                    </div>
                                                    <div class="flex items-center justify-between gap-2">
                                                        <span class="text-[10px] font-bold text-gray-500 w-6">إلى:</span>
                                                        <input type="time" :name="'schedules[' + dayName + '][shifts]['+index+'][end]'" x-model="shift.end" class="px-2 py-1 bg-white border border-gray-200 rounded text-xs font-bold text-gray-700 focus:border-teal-custom" dir="ltr" required>
                                                    </div>
                                                </div>
                                                
                                                <!-- Remove Shift Button -->
                                                <button type="button" @click="removeShift(index)" x-show="shifts.length > 1" class="absolute -top-2 -left-2 w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center text-[10px] opacity-0 group-hover:opacity-100 transition shadow">
                                                    <i class="fa-solid fa-xmark"></i>
                                                </button>
                                            </div>
                                        </template>

                                        <!-- Add Shift Button -->
                                        <button type="button" @click="addShift()" class="flex flex-col items-center justify-center gap-1 bg-teal-50/50 hover:bg-teal-50 border border-dashed border-teal-300 text-teal-600 rounded-xl p-4 transition min-w-[120px]">
                                            <i class="fa-solid fa-plus"></i>
                                            <span class="text-xs font-bold">فترة جديدة</span>
                                        </button>
                                        
                                    </div>
                                </div>

                                <!-- Off Day -->
                                <div class="p-5 text-center" x-show="!isWorking">
                                    <span class="text-gray-400 font-bold text-sm"><i class="fa-solid fa-mug-hot"></i> إجازة أسبوعية</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex justify-between items-center border-t border-gray-100 pt-6">
                        <p class="text-xs text-gray-500 font-bold"><i class="fa-solid fa-circle-info text-blue-500"></i> سيتم حساب السلوتس (مواعيد الحجز المتاحة) تلقائياً بناءً على فترات العمل ومدة الكشف.</p>
                        <button type="submit" class="bg-teal-custom hover:bg-teal-700 text-white px-8 py-3 rounded-xl text-sm font-bold transition shadow-sm flex items-center gap-2">
                            <i class="fa-solid fa-check-double"></i> حفظ الجدول والفترات
                        </button>
                    </div>
                </form>
            </div>

            <!-- Doctor Leaves & Vacations -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden mt-6">
                <div class="bg-orange-50/50 border-b border-orange-100 px-6 py-4">
                    <h3 class="font-black text-orange-800 flex items-center gap-2">
                        <i class="fa-solid fa-umbrella-beach text-orange-500"></i> إدارة الإجازات والاستثناءات
                    </h3>
                </div>
                
                <div class="p-6">
                    <form action="{{ route('doctor-leaves.store') }}" method="POST" class="flex flex-wrap md:flex-nowrap gap-4 items-end mb-8 bg-gray-50 p-4 rounded-2xl border border-gray-100">
                        @csrf
                        <input type="hidden" name="doctor_id" value="{{ $selectedDoctor->id }}">
                        
                        <div class="flex-1 w-full md:w-auto">
                            <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">السبب / الوصف</label>
                            <input type="text" name="reason" placeholder="إجازة سنوية، مؤتمر طبي، ظرف طارئ..." class="w-full px-4 py-2.5 bg-white border border-gray-200 focus:border-teal-custom rounded-xl text-sm transition font-bold shadow-sm">
                        </div>
                        <div class="w-full md:w-48">
                            <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">من تاريخ <span class="text-red-500">*</span></label>
                            <input type="date" name="from_date" required class="w-full px-4 py-2.5 bg-white border border-gray-200 focus:border-teal-custom rounded-xl text-sm transition font-bold shadow-sm text-right">
                        </div>
                        <div class="w-full md:w-48">
                            <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">إلى تاريخ <span class="text-red-500">*</span></label>
                            <input type="date" name="to_date" required class="w-full px-4 py-2.5 bg-white border border-gray-200 focus:border-teal-custom rounded-xl text-sm transition font-bold shadow-sm text-right">
                        </div>
                        <div class="w-full md:w-auto">
                            <button type="submit" class="w-full bg-orange-100 hover:bg-orange-200 text-orange-700 border border-orange-200 px-6 py-2.5 rounded-xl text-sm font-bold transition shadow-sm flex items-center justify-center gap-2">
                                <i class="fa-solid fa-plus"></i> تسجيل الإجازة
                            </button>
                        </div>
                    </form>

                    @if($leaves->count() > 0)
                        <div class="border border-gray-100 rounded-2xl overflow-hidden">
                            <table class="w-full text-right text-sm">
                                <thead class="bg-gray-50 text-gray-600 font-bold border-b border-gray-100">
                                    <tr>
                                        <th class="px-6 py-4">السبب / الوصف</th>
                                        <th class="px-6 py-4 text-center">من تاريخ</th>
                                        <th class="px-6 py-4 text-center">إلى تاريخ</th>
                                        <th class="px-6 py-4 text-center">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 text-gray-700">
                                    @foreach($leaves as $leave)
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="px-6 py-4 font-bold text-gray-800">{{ $leave->reason ?? 'إجازة مجدولة' }}</td>
                                        <td class="px-6 py-4 text-center font-mono text-gray-600 bg-red-50/30">{{ $leave->from_date->format('Y-m-d') }}</td>
                                        <td class="px-6 py-4 text-center font-mono text-gray-600 bg-red-50/30">{{ $leave->to_date->format('Y-m-d') }}</td>
                                        <td class="px-6 py-4 text-center">
                                            <form action="{{ route('doctor-leaves.destroy', $leave->id) }}" method="POST" class="inline-block" onsubmit="return confirm('هل أنت متأكد من إلغاء وحذف هذه الإجازة؟');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-400 hover:text-red-600 text-xs font-bold transition bg-white border border-gray-200 hover:border-red-200 px-3 py-1.5 rounded-lg shadow-sm">
                                                    إلغاء الإجازة
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fa-solid fa-umbrella-beach text-4xl text-gray-200 mb-3"></i>
                            <p class="text-sm font-bold text-gray-500">لا توجد إجازات أو استثناءات مسجلة لهذا الطبيب.</p>
                        </div>
                    @endif
                </div>
            </div>
            
        @endif
    </div>

</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('scheduleDay', (dayName, initialWorking, initialDuration, initialShifts) => ({
            dayName: dayName,
            isWorking: initialWorking,
            duration: initialDuration,
            shifts: initialShifts,
            
            addShift() {
                this.shifts.push({ start: '16:00', end: '20:00' });
            },
            
            removeShift(index) {
                if(this.shifts.length > 1) {
                    this.shifts.splice(index, 1);
                }
            }
        }));
    });
</script>
@endsection
