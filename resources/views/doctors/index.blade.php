@extends('layouts.app')

@section('title', 'الأطباء والفريق - فكرتك الرقمية')

@section('content')
<div x-data="{ isModalOpen: false, modalTitle: 'بيانات الطبيب' }">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-teal-custom mb-1">الأطباء والفريق</h1>
            <p class="text-gray-500 text-sm">
                {{ $totalDoctors }} أطباء - {{ $totalNurses }} ممرض - {{ $onLeave }} إجازة - {{ $present }} متواجد
            </p>
        </div>
        <button @click="isModalOpen = true" class="bg-teal-custom hover:bg-teal-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> إضافة طبيب جديد
        </button>
    </div>

    <!-- Filters & Search -->
    <div class="flex flex-wrap md:flex-nowrap gap-3 mb-6 items-center">
        <!-- Filter Pills -->
        <div class="flex gap-2 text-sm font-medium">
            <a href="{{ route('doctors.index') }}" class="px-5 py-2 rounded-full transition {{ !request('status') ? 'bg-teal-custom text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                الكل ({{ $totalDoctors }})
            </a>
            <a href="{{ route('doctors.index', ['status' => 'present']) }}" class="px-5 py-2 rounded-full transition {{ request('status') == 'present' ? 'bg-teal-custom text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                حاضر ({{ $present }})
            </a>
            <a href="{{ route('doctors.index', ['status' => 'absent']) }}" class="px-5 py-2 rounded-full transition {{ request('status') == 'absent' ? 'bg-teal-custom text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                إجازة ({{ $onLeave }})
            </a>
            <a href="#" class="px-5 py-2 rounded-full transition bg-white border border-gray-200 text-gray-600 hover:bg-gray-50">
                مناوب (0)
            </a>
        </div>

        <div class="flex-1"></div>

        <!-- Search Bar -->
        <form action="{{ route('doctors.index') }}" method="GET" class="relative w-full md:w-80">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث باسم الطبيب أو التخصص..." class="w-full px-4 py-2 border border-gray-200 rounded-full focus:outline-none focus:border-teal-custom text-sm text-right pr-10">
            <button type="submit" class="absolute right-3 top-2.5 text-gray-400 hover:text-teal-custom">
                <i class="fa-solid fa-search"></i>
            </button>
        </form>
    </div>

    <!-- Doctors Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($doctors as $doctor)
        <a href="{{ route('doctors.show', $doctor->id) }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col relative overflow-hidden group hover:border-teal-custom hover:shadow-md transition">
            
            <!-- Right edge green bar -->
            <div class="absolute top-0 right-0 h-full w-1 {{ $doctor->is_present ? 'bg-teal-500' : 'bg-gray-300' }}"></div>

            <!-- Top Row: Badge & Info -->
            <div class="flex justify-between items-start mb-6">
                <!-- Status Badge -->
                @if($doctor->is_present)
                    <span class="text-[10px] font-bold text-teal-600 bg-teal-50 px-2 py-1 rounded">حاضر</span>
                @else
                    <span class="text-[10px] font-bold text-gray-500 bg-gray-100 px-2 py-1 rounded">إجازة</span>
                @endif

                <!-- Doctor Info -->
                <div class="flex items-center gap-3">
                    <div class="text-right">
                        <h3 class="font-bold text-gray-800 text-sm group-hover:text-teal-custom transition">{{ $doctor->name }}</h3>
                        <p class="text-xs text-gray-400 mt-1">{{ $doctor->specialization ?? 'عام' }}</p>
                    </div>
                    <!-- Initials Circle -->
                    <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-teal-custom text-sm font-bold shrink-0">
                        {{ mb_substr(str_replace('د. ', '', $doctor->name), 0, 2) }}
                    </div>
                </div>
            </div>

            <!-- Bottom Row: Stats -->
            <div class="flex justify-between items-end border-t border-gray-50 pt-4 mt-auto">
                <div class="text-center w-1/2 border-l border-gray-100">
                    <span class="block text-lg font-bold text-gray-800">—</span>
                    <span class="text-[10px] text-gray-400">إنجاز المواعيد</span>
                </div>
                <div class="text-center w-1/2">
                    <span class="block text-lg font-bold text-gray-800">0</span>
                    <span class="text-[10px] text-gray-400">مريض اليوم</span>
                </div>
            </div>
            
        </a>
        @endforeach

        <!-- Add New Card (Empty State) -->
        <div @click="isModalOpen = true" class="bg-gray-50 border-2 border-dashed border-gray-200 rounded-xl flex flex-col items-center justify-center p-8 text-gray-400 hover:bg-gray-100 hover:text-teal-custom hover:border-teal-custom transition cursor-pointer min-h-[160px]">
            <i class="fa-solid fa-plus text-2xl mb-2"></i>
            <span class="font-medium text-sm">إضافة طبيب جديد</span>
        </div>
    </div>

    <!-- Add Doctor Modal -->
    <div x-show="isModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            
            <div x-show="isModalOpen" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" aria-hidden="true" @click="isModalOpen = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="isModalOpen" x-transition class="inline-block align-bottom bg-white rounded-xl text-right overflow-visible shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
                
                <form action="{{ route('doctors.store') }}" method="POST">
                    @csrf

                    <!-- Modal Header -->
                    <div class="bg-white px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <button type="button" @click="isModalOpen = false" class="text-gray-400 hover:text-gray-500 focus:outline-none w-8 h-8 flex items-center justify-center rounded-full bg-gray-50 hover:bg-gray-100">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                        <h3 class="text-lg leading-6 font-bold text-teal-custom" id="modal-title" x-text="modalTitle"></h3>
                    </div>

                    <!-- Modal Body -->
                    <div class="bg-white px-8 py-6 space-y-6">
                        
                        <!-- Personal Data -->
                        <div>
                            <h4 class="text-sm font-bold text-teal-custom mb-4">البيانات الشخصية</h4>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-2">الاسم الكامل</label>
                                    <input type="text" name="name" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom">
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-2">الجوال (اختياري)</label>
                                        <input type="text" name="phone" dir="ltr" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom text-right">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-2">البريد الداخلي (اختياري)</label>
                                        <input type="email" name="email" dir="ltr" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom text-right">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Professional Data -->
                        <div>
                            <h4 class="text-sm font-bold text-teal-custom mb-4">البيانات المهنية</h4>
                            <div class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-2">التخصص</label>
                                        <select name="specialization" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom bg-white">
                                            <option value="" disabled selected>اختر</option>
                                            <option value="الجلدية">الجلدية</option>
                                            <option value="الباطنة">الباطنة</option>
                                            <option value="الأطفال">الأطفال</option>
                                            <option value="النساء والولادة">النساء والولادة</option>
                                            <option value="العظام">العظام</option>
                                            <option value="عام">عام</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-2">العيادة / القسم</label>
                                        <select name="clinic_id" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom bg-white">
                                            <option value="" disabled selected>اختر</option>
                                            @foreach($clinics as $clinic)
                                                <option value="{{ $clinic->id }}">{{ $clinic->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-2">مدة الكشف (دقيقة)</label>
                                        <input type="number" name="consultation_duration" value="20" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom text-right">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-2">رقم الترخيص</label>
                                        <input type="text" name="license_number" dir="ltr" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom text-right">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Modal Footer -->
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-start gap-3">
                        <button type="submit" class="bg-teal-custom hover:bg-teal-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition">
                            حفظ بيانات الطبيب
                        </button>
                        <button type="button" @click="isModalOpen = false" class="border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 px-6 py-2 rounded-lg text-sm font-medium transition">
                            إلغاء
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
