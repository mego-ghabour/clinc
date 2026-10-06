@extends('layouts.app')

@section('title', 'العيادات والأقسام - فكرتك الرقمية')

@section('content')
<div x-data="clinicManager()">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-teal-custom mb-1">العيادات والأقسام</h1>
            <p class="text-gray-500 text-sm">12 عيادة مسجلة - 12 نشطة، 0 متوقفة</p>
        </div>
        <button @click="openAddModal()" class="bg-teal-custom hover:bg-teal-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm flex items-center gap-2">
            <i class="fa-solid fa-plus"></i>
            إضافة عيادة جديدة
        </button>
    </div>

    <!-- Filters -->
    <div class="flex items-center gap-3 mb-6">
        <button @click="filterStatus = 'all'" :class="filterStatus === 'all' ? 'bg-teal-custom text-white' : 'bg-white border border-gray-200 text-gray-600'" class="px-4 py-1.5 rounded-full text-sm transition">الكل ({{ $clinics->whereNull('deleted_at')->count() }})</button>
        <button @click="filterStatus = 'نشطة'" :class="filterStatus === 'نشطة' ? 'bg-teal-custom text-white' : 'bg-white border border-gray-200 text-gray-600'" class="px-4 py-1.5 rounded-full text-sm transition">نشطة ({{ $clinics->whereNull('deleted_at')->where('status', 'نشطة')->count() }})</button>
        <button @click="filterStatus = 'متوقفة'" :class="filterStatus === 'متوقفة' ? 'bg-teal-custom text-white' : 'bg-white border border-gray-200 text-gray-600'" class="px-4 py-1.5 rounded-full text-sm transition">متوقفة ({{ $clinics->whereNull('deleted_at')->where('status', 'متوقفة')->count() }})</button>
        <button @click="filterStatus = 'trashed'" :class="filterStatus === 'trashed' ? 'bg-red-500 text-white border-transparent' : 'bg-white border border-red-200 text-red-500 hover:bg-red-50'" class="px-4 py-1.5 rounded-full text-sm transition">سلة المحذوفات ({{ $clinics->whereNotNull('deleted_at')->count() }})</button>
        <div class="flex-1"></div>
        <div class="relative w-64">
            <input type="text" x-model="searchQuery" placeholder="ابحث باسم العيادة أو التخصص..." class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom text-sm text-right bg-white transition">
            <i class="fa-solid fa-search absolute left-3 top-3 text-gray-400"></i>
        </div>
    </div>

    <!-- Clinics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @forelse($clinics as $clinic)
        @php
            $clinicIcon = $clinic->icon ?? 'fa-stethoscope';
        @endphp
        <div x-show="matchesSearch('{{ $clinic->name }}', '{{ $clinic->status }}', {{ $clinic->trashed() ? 'true' : 'false' }})" x-transition.opacity onclick="window.location.href='{{ route('clinics.show', $clinic->id) }}'" class="cursor-pointer bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex flex-col relative group hover:-translate-y-1 hover:shadow-lg transition-all duration-300" style="border-top-width: 4px; border-top-color: {{ $clinic->color ?? '#14b8a6' }};">
            
            <div class="flex justify-between items-start mb-4">
                <!-- Right Side: Icon + Name + Code -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 overflow-hidden" style="background-color: {{ $clinic->color ? $clinic->color.'20' : '#14b8a620' }}; color: {{ $clinic->color ?? '#14b8a6' }};">
                        @if($clinicIcon && str_contains($clinicIcon, '.'))
                            <img src="{{ asset('storage/' . $clinicIcon) }}" class="w-full h-full object-cover" alt="أيقونة العيادة">
                        @else
                            <i class="fa-solid {{ $clinicIcon }} text-lg"></i>
                        @endif
                    </div>
                    <div class="text-right">
                        <a href="{{ route('clinics.show', $clinic->id) }}" onclick="event.stopPropagation()" class="inline-block hover:text-teal-custom transition-colors">
                            <h3 class="font-bold text-lg text-gray-800 hover:text-teal-custom">{{ $clinic->name }}</h3>
                        </a>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="text-xs text-gray-400 font-medium">رمز: {{ $clinic->code ?? '00' }}</span>
                            @if($clinic->headDoctor)
                            <span class="text-[10px] bg-purple-50 text-purple-600 px-1.5 py-0.5 rounded border border-purple-100" title="رئيس القسم">
                                <i class="fa-solid fa-crown mr-1"></i> {{ $clinic->headDoctor->name }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Left Side: Status -->
                <div class="flex flex-col items-end gap-1">
                    @if($clinic->trashed())
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-red-50 text-red-600 border border-red-100">مؤرشفة</span>
                    @else
                        <span class="text-xs font-bold px-2 py-1 rounded bg-teal-50 text-teal-600">{{ $clinic->status }}</span>
                    @endif
                </div>
            </div>

            <div class="mb-4 flex-1">
                @if($clinic->doctors && $clinic->doctors->count() > 0)
                    <div class="mt-2 text-xs flex flex-col gap-1.5 items-center w-full">
                        @foreach($clinic->doctors as $doctor)
                            <a href="{{ route('doctors.show', $doctor->id) }}" onclick="event.stopPropagation()" class="flex items-center justify-between bg-gray-50 border border-gray-200 py-1.5 px-3 rounded-lg w-full hover:bg-teal-50 hover:border-teal-200 hover:shadow-sm transition-all cursor-pointer group/doc">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-white flex items-center justify-center border border-gray-100 text-gray-400 group-hover/doc:text-teal-custom transition-colors shadow-sm">
                                        <i class="fa-solid fa-user-doctor text-[10px]"></i>
                                    </div>
                                    <span class="text-gray-600 font-medium text-xs group-hover/doc:text-teal-700 transition-colors">{{ $doctor->name }}</span>
                                </div>
                                <div class="flex items-center" title="متاح الآن">
                                    <span class="relative flex h-2 w-2">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                                    </span>
                                </div>
                            </a>
                                @endforeach
                    </div>
                @else
                    <p class="text-xs text-orange-500 mt-2 bg-orange-50 py-1 px-2 rounded inline-block">
                        <i class="fa-solid fa-triangle-exclamation"></i> لا يوجد طبيب مناوب حالياً
                    </p>
                @endif
            </div>

            <div class="flex items-center gap-3 border-t border-gray-100 pt-3 mt-1 mb-2">
                <div class="text-center bg-teal-50/50 px-3 py-1.5 rounded-lg border border-teal-100/50 flex-1">
                    <span class="block text-lg font-bold text-teal-600 leading-tight">0</span>
                    <span class="text-[10px] font-medium text-teal-600/80">منجز اليوم</span>
                </div>
                <div class="text-center bg-red-50/50 px-3 py-1.5 rounded-lg border border-red-100/50 flex-1">
                    <span class="block text-lg font-bold text-red-600 leading-tight">0</span>
                    <span class="text-[10px] font-medium text-red-600/80">في الانتظار</span>
                </div>
            </div>

            <!-- Action Dropdown (Bottom Left) -->
            <div onclick="event.stopPropagation()" class="flex justify-end mt-auto pt-3 border-t border-gray-50/50 relative z-10" x-data="{ open: false, showDeleteConfirm: false }">
                <button @click="open = !open" @click.away="open = false" class="text-xs bg-gray-50 hover:bg-gray-100 border border-gray-200 text-gray-600 px-3 py-1.5 rounded-lg transition flex items-center gap-1.5 shadow-sm">
                    إجراءات <i class="fa-solid fa-caret-down text-[10px]"></i>
                </button>
                <div x-show="open" class="absolute bottom-10 left-0 w-44 bg-white border border-gray-100 shadow-xl rounded-lg py-1 z-20" style="display: none;" x-transition>
                    @if($clinic->trashed())
                        <form action="{{ route('clinics.restore', $clinic->id) }}" method="POST" class="block">
                            @csrf
                            <button type="submit" class="w-full text-right px-4 py-2 text-sm text-green-600 hover:bg-green-50 flex items-center gap-2">
                                <i class="fa-solid fa-arrow-rotate-left w-4"></i> استعادة
                            </button>
                        </form>
                        <button @click="open = false; openDeleteModal({{ $clinic }})" class="w-full text-right px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2 border-t border-gray-50">
                            <i class="fa-solid fa-trash-can w-4"></i> حذف نهائي
                        </button>
                    @else
                        <a href="{{ route('clinics.show', $clinic->id) }}" class="w-full text-right px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2 whitespace-nowrap">
                            <i class="fa-regular fa-eye text-teal-custom w-4"></i> عرض التفاصيل
                        </a>
                        <button @click="openEditModal({{ $clinic }})" class="w-full text-right px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2 border-t border-gray-50">
                            <i class="fa-solid fa-pen-to-square text-teal-custom w-4"></i> تعديل
                        </button>
                        <button @click="open = false; openDeleteModal({{ $clinic }})" class="w-full text-right px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2 border-t border-gray-50">
                            <i class="fa-solid fa-trash w-4"></i> أرشفة
                        </button>
                    @endif
                </div>
            </div>


            
        </div>
                @empty
        <div class="col-span-1 md:col-span-2 lg:col-span-4 flex flex-col items-center justify-center bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
            <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                <i class="fa-solid fa-hospital text-4xl text-gray-300"></i>
            </div>
            <h2 class="text-xl font-bold text-gray-800 mb-2">?? ???? ?????? ?????</h2>
            <p class="text-gray-500 mb-6 max-w-md">?? ??? ?????? ?? ?????? ?? ????? ??? ????. ????? ????? ?????? ??????? ?????? ?????? ????? ?????? ???????.</p>
            <button @click="openAddModal()" class="bg-teal-custom hover:bg-teal-700 text-white px-6 py-3 rounded-lg font-bold transition flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-plus"></i>
                ????? ??????? ??????
            </button>
        </div>
        @endforelse

        <!-- Add New Card (Empty State) -->
        <div @click="openAddModal()" class="bg-gray-50 border-2 border-dashed border-gray-200 rounded-xl flex flex-col items-center justify-center p-8 text-gray-400 hover:bg-gray-100 hover:text-teal-custom hover:border-teal-custom transition cursor-pointer min-h-[220px]">
            <i class="fa-solid fa-plus text-3xl mb-3"></i>
            <span class="font-medium text-sm">إضافة عيادة جديدة</span>
        </div>
    </div>

    <!-- Modal Background -->
    <div x-show="isModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            
            <div x-show="isModalOpen" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" aria-hidden="true" @click="closeModal()"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="isModalOpen" x-transition class="inline-block align-bottom bg-white rounded-xl text-right overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                
                <form :action="formAction" method="POST" enctype="multipart/form-data">
                    @csrf
                    <!-- We use x-html to inject @method('PUT') when editing -->
                    <div x-html="methodInput"></div>

                    <!-- Modal Header -->
                    <div class="bg-white px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-lg leading-6 font-bold text-teal-custom" id="modal-title" x-text="modalTitle"></h3>
                        <button type="button" @click="closeModal()" class="text-gray-400 hover:text-gray-500 focus:outline-none w-8 h-8 flex items-center justify-center rounded-full bg-gray-50 hover:bg-gray-100">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="bg-white px-6 py-6 space-y-5">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-2 text-right">اسم العيادة / القسم</label>
                                <input type="text" name="name" x-model="form.name" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom text-right bg-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-2 text-right">أيقونة العيادة (صورة)</label>
                                <input type="file" name="icon_file" accept="image/*" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom text-right bg-white text-sm">
                                <p class="text-xs text-gray-400 mt-1 text-right">اختياري، يفضل صورة بخلفية شفافة</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-2 text-right">لون القسم (التمييز اللوني)</label>
                                <input type="color" name="color" x-model="form.color" class="w-full h-10 px-1 py-1 border border-gray-200 rounded-lg focus:outline-none bg-white cursor-pointer">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-2 text-right">رئيس القسم (اختياري)</label>
                                <select name="head_doctor_id" x-model="form.head_doctor_id" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom text-right bg-white">
                                    <option value="">بدون رئيس قسم حالياً</option>
                                    @foreach($doctors as $doc)
                                        <option value="{{ $doc->id }}">{{ $doc->name }}</option>
                                            @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-2">الرمز</label>
                                <input type="text" name="code" x-model="form.code" dir="ltr" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom text-right">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-2">حالة العيادة</label>
                                <select name="status" x-model="form.status" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom bg-white">
                                    <option value="نشطة">نشطة</option>
                                    <option value="متوقفة">متوقفة</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-2 text-right">الطاقة الاستيعابية</label>
                                <input type="number" name="daily_quota" x-model="form.daily_quota" min="1" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom text-right bg-white" placeholder="مثال: 30">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-2 text-right">بداية الدوام</label>
                                <input type="time" name="start_time" x-model="form.start_time" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom text-right bg-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-2 text-right">نهاية الدوام</label>
                                <input type="time" name="end_time" x-model="form.end_time" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom text-right bg-white">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2 text-right">ملاحظات / الموقع</label>
                            <textarea name="notes" x-model="form.notes" rows="3" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom resize-none text-right bg-gray-50"></textarea>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-end gap-3 items-center">
                        <button type="button" @click="closeModal()" class="border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 px-6 py-2 rounded-lg text-sm font-medium transition">
                            إلغاء
                        </button>
                        <button type="submit" class="bg-teal-custom hover:bg-teal-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition">
                            حفظ العيادة
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
    const registerClinicManager = () => {
        Alpine.data('clinicManager', () => ({
            isModalOpen: false,
            searchQuery: '',
            filterStatus: 'all',
            modalTitle: 'بيانات العيادة',
            formAction: '{{ route('clinics.store') }}',
            methodInput: '',
            
            matchesSearch(name, status, isTrashed = false) {
                const matchesText = name.toLowerCase().includes(this.searchQuery.toLowerCase());
                let matchesStatus = false;
                
                if (this.filterStatus === 'trashed') {
                    matchesStatus = isTrashed;
                } else if (isTrashed) {
                    matchesStatus = false;
                } else {
                    matchesStatus = this.filterStatus === 'all' || this.filterStatus === status;
                }
                
                return matchesText && matchesStatus;
            },
            form: {
                id: null,
                name: '',
                icon: 'fa-stethoscope',
                color: '#14b8a6',
                head_doctor_id: '',
                code: '',
                status: 'نشطة',
                daily_quota: '',
                start_time: '',
                end_time: '',
                notes: ''
            },
            
            openAddModal() {
                this.modalTitle = 'إضافة عيادة جديدة';
                this.formAction = '{{ route('clinics.store') }}';
                this.methodInput = '';
                this.form = { id: null, name: '', icon: 'fa-stethoscope', color: '#14b8a6', head_doctor_id: '', code: '', status: 'نشطة', daily_quota: '', start_time: '', end_time: '', notes: '' };
                this.isModalOpen = true;
            },
            
            openEditModal(clinic) {
                this.modalTitle = 'تعديل بيانات العيادة';
                this.formAction = `/clinics/${clinic.id}`;
                this.methodInput = '<input type="hidden" name="_method" value="PUT">';
                // Extract only HH:mm from time strings if they exist
                let startTime = clinic.start_time ? clinic.start_time.substring(0, 5) : '';
                let endTime = clinic.end_time ? clinic.end_time.substring(0, 5) : '';
                
                this.form = { 
                    id: clinic.id, 
                    name: clinic.name,
                    icon: clinic.icon || 'fa-stethoscope',
                    color: clinic.color || '#14b8a6',
                    head_doctor_id: clinic.head_doctor_id || '',
                    code: clinic.code || '',
                    status: clinic.status,
                    daily_quota: clinic.daily_quota || '',
                    start_time: startTime,
                    end_time: endTime,
                    notes: clinic.notes || ''
                };
                this.isModalOpen = true;
            },
            
            closeModal() {
                this.isModalOpen = false;
            }
        }));
    };

    if (window.Alpine) {
        registerClinicManager();
    } else {
        document.addEventListener('alpine:init', registerClinicManager);
    }
</script>
@endsection

