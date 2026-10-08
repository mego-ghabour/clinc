@extends('layouts.app')

@section('title', 'إدارة الطابور والاستقبال - Clinc')
@section('header', 'شاشة الاستقبال وطابور الانتظار لليوم')

@section('content')
<div class="space-y-6">

    <!-- Header & Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center text-xl font-black">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-500 mb-0.5">إجمالي حجوزات اليوم</p>
                <h3 class="text-2xl font-black text-gray-800">{{ $expected->count() + $waiting->count() }}</h3>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-yellow-50 text-yellow-500 flex items-center justify-center text-xl font-black">
                <i class="fa-solid fa-hourglass-half"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-500 mb-0.5">لم يحضروا بعد</p>
                <h3 class="text-2xl font-black text-gray-800">{{ $expected->count() }}</h3>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-xl font-black">
                <i class="fa-solid fa-user-clock"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-500 mb-0.5">في الطابور (الانتظار)</p>
                <h3 class="text-2xl font-black text-teal-600">{{ $waiting->count() }}</h3>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-500 mb-1">تاريخ اليوم</p>
                <h3 class="text-lg font-black text-gray-800 font-mono" dir="ltr">{{ date('Y-m-d') }}</h3>
            </div>
            <div class="text-teal-200 text-3xl">
                <i class="fa-solid fa-calendar-day"></i>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Expected Arrivals (لم يحضروا بعد) -->
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-[700px]">
            <div class="bg-gray-50 border-b border-gray-100 p-5 flex justify-between items-center shrink-0">
                <h3 class="font-black text-gray-800 flex items-center gap-2 text-lg">
                    <span class="w-8 h-8 rounded-full bg-yellow-100 text-yellow-600 flex items-center justify-center text-sm shadow-sm"><i class="fa-solid fa-walking"></i></span>
                    حجوزات يتوقع وصولها
                </h3>
                <span class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-3 py-1 rounded-full text-xs font-bold">{{ $expected->count() }} مريض</span>
            </div>
            
            <div class="flex-1 overflow-y-auto p-5 space-y-4 custom-scrollbar">
                @forelse($expected as $appointment)
                <div class="bg-white border border-gray-100 hover:border-yellow-200 shadow-sm hover:shadow rounded-2xl p-4 transition group">
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gray-50 border border-gray-200 flex items-center justify-center text-gray-400 font-black">
                                {{ mb_substr($appointment->patient->name, 0, 1) }}
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800 hover:text-teal-600 transition">
                                    <a href="{{ route('patients.show', $appointment->patient_id) }}">{{ $appointment->patient->name }}</a>
                                </h4>
                                <div class="text-xs text-gray-500 font-mono mt-0.5"><i class="fa-solid fa-mobile-screen text-gray-400"></i> {{ $appointment->patient->mobile ?? 'لا يوجد' }}</div>
                            </div>
                        </div>
                        <div class="text-left">
                            <div class="font-mono font-black text-lg text-gray-800" dir="ltr">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}</div>
                            <div class="text-[10px] text-gray-400 font-bold">موعد الحجز</div>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between border-t border-gray-50 pt-3">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-teal-700 bg-teal-50 px-2 py-1 rounded border border-teal-100">د. {{ $appointment->doctor->name }}</span>
                            <span class="text-xs font-bold text-gray-500">{{ $appointment->service }}</span>
                        </div>
                        
                        <form action="{{ route('queues.checkin', $appointment->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-teal-custom hover:bg-teal-700 text-white px-4 py-2 rounded-xl text-xs font-bold transition shadow-sm flex items-center gap-1.5 opacity-90 hover:opacity-100">
                                <i class="fa-solid fa-check-to-slot"></i> تسجيل حضور
                            </button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="h-full flex flex-col items-center justify-center text-gray-400">
                    <i class="fa-regular fa-calendar-check text-5xl mb-3 text-gray-200"></i>
                    <p class="font-bold text-sm">لا توجد حجوزات متوقعة حالياً</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Waiting Queue (في الطابور) -->
        <div class="bg-white rounded-3xl shadow-sm border border-teal-100 overflow-hidden flex flex-col h-[700px] relative">
            <div class="absolute top-0 right-0 w-32 h-32 bg-teal-50 rounded-full -mr-16 -mt-16 opacity-50 z-0"></div>
            
            <div class="bg-teal-custom border-b border-teal-700 p-5 flex justify-between items-center shrink-0 relative z-10">
                <h3 class="font-black text-white flex items-center gap-2 text-lg">
                    <span class="w-8 h-8 rounded-full bg-white/20 text-white flex items-center justify-center text-sm shadow-sm"><i class="fa-solid fa-users-rays"></i></span>
                    طابور الانتظار المباشر (LIVE)
                </h3>
                <span class="bg-white text-teal-700 px-3 py-1 rounded-full text-xs font-bold shadow-sm">{{ $waiting->count() }} مريض</span>
            </div>
            
            <div class="flex-1 overflow-y-auto p-5 space-y-4 custom-scrollbar relative z-10 bg-teal-50/10">
                @forelse($waiting as $index => $appointment)
                <div class="bg-white border-2 border-teal-100 hover:border-teal-300 shadow-sm rounded-2xl p-4 transition relative overflow-hidden">
                    
                    <div class="absolute top-0 right-0 w-8 h-full bg-teal-50 flex items-center justify-center font-black text-teal-200 text-xl border-l border-teal-100">
                        {{ $index + 1 }}
                    </div>

                    <div class="pr-10">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h4 class="font-bold text-gray-800 text-lg">
                                    <a href="{{ route('patients.show', $appointment->patient_id) }}">{{ $appointment->patient->name }}</a>
                                </h4>
                                <div class="text-xs text-gray-500 font-mono mt-0.5"><i class="fa-solid fa-id-card text-gray-400"></i> MRN: {{ $appointment->patient->mrn }}</div>
                            </div>
                            <div class="text-left bg-teal-50 border border-teal-100 px-3 py-1.5 rounded-lg text-teal-700 font-bold text-xs flex items-center gap-1.5 shadow-sm">
                                <span class="relative flex h-2 w-2">
                                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-teal-400 opacity-75"></span>
                                  <span class="relative inline-flex rounded-full h-2 w-2 bg-teal-500"></span>
                                </span>
                                في الانتظار
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between border-t border-gray-100 pt-3 mt-2">
                            <div class="flex flex-col gap-1">
                                <span class="text-xs font-bold text-gray-600"><i class="fa-solid fa-user-doctor text-gray-400"></i> العيادة: <span class="text-teal-700">د. {{ $appointment->doctor->name }}</span></span>
                            </div>
                            
                            <!-- Send to Nurse / Doctor -->
                            @php
                                $visit = \App\Models\Visit::where('appointment_id', $appointment->id)->first();
                            @endphp
                            
                            @if($visit)
                                <a href="{{ route('visits.show', $visit->id) }}" class="bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white border border-blue-200 px-4 py-2 rounded-xl text-xs font-bold transition shadow-sm flex items-center gap-1.5">
                                    <i class="fa-solid fa-stethoscope"></i> فتح الملف الطبي (EMR)
                                </a>
                            @else
                                <span class="text-xs text-gray-400">جاري التجهيز...</span>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="h-full flex flex-col items-center justify-center text-teal-700/30">
                    <i class="fa-solid fa-mug-hot text-6xl mb-4"></i>
                    <p class="font-bold text-base">طابور الانتظار فارغ حالياً</p>
                    <p class="text-xs mt-1">الاستقبال يعمل بشكل رائع!</p>
                </div>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection
