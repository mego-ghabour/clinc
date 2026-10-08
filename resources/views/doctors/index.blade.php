@extends('layouts.app')

@section('title', 'إدارة الأطباء')
@section('header', 'دليل الأطباء والفريق الطبي')

@section('content')
<div x-data="doctorManager()">
    <!-- Stats Header -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-teal-50 border border-teal-100 rounded-2xl p-4 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-teal-500 text-white flex items-center justify-center text-xl shrink-0 shadow-sm">
                <i class="fa-solid fa-user-doctor"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-teal-600 mb-0.5">إجمالي الأطباء</p>
                <h4 class="text-xl font-black text-teal-900">{{ $totalDoctors }}</h4>
            </div>
        </div>
        <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-blue-500 text-white flex items-center justify-center text-xl shrink-0 shadow-sm">
                <i class="fa-solid fa-user-nurse"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-blue-600 mb-0.5">التمريض</p>
                <h4 class="text-xl font-black text-blue-900">{{ $totalNurses }}</h4>
            </div>
        </div>
        <div class="bg-green-50 border border-green-100 rounded-2xl p-4 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-green-500 text-white flex items-center justify-center text-xl shrink-0 shadow-sm">
                <i class="fa-solid fa-clipboard-check"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-green-600 mb-0.5">متاحين الآن (حاضر)</p>
                <h4 class="text-xl font-black text-green-900">{{ $present }}</h4>
            </div>
        </div>
        <div class="bg-orange-50 border border-orange-100 rounded-2xl p-4 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-orange-500 text-white flex items-center justify-center text-xl shrink-0 shadow-sm">
                <i class="fa-solid fa-plane-departure"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-orange-600 mb-0.5">في إجازة (غائب)</p>
                <h4 class="text-xl font-black text-orange-900">{{ $onLeave }}</h4>
            </div>
        </div>
    </div>

    <!-- Toolbar -->
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-8 flex flex-col md:flex-row gap-4 items-center justify-between">
        <!-- Filter Tabs -->
        <div class="flex bg-gray-50 p-1 rounded-xl w-full md:w-auto overflow-x-auto">
            <a href="{{ route('doctors.index') }}" class="flex-none text-center px-6 py-2 rounded-lg text-sm font-bold transition {{ !request('status') || request('status') == 'all' ? 'bg-white text-teal-custom shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                الكل
            </a>
            <a href="{{ route('doctors.index', ['status' => 'present', 'search' => request('search')]) }}" class="flex-none text-center px-6 py-2 rounded-lg text-sm font-bold transition {{ request('status') == 'present' ? 'bg-white text-green-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                متاح الآن
            </a>
            <a href="{{ route('doctors.index', ['status' => 'absent', 'search' => request('search')]) }}" class="flex-none text-center px-6 py-2 rounded-lg text-sm font-bold transition {{ request('status') == 'absent' ? 'bg-white text-orange-500 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                في إجازة
            </a>
            <a href="{{ route('doctors.index', ['status' => 'suspended', 'search' => request('search')]) }}" class="flex-none text-center px-6 py-2 rounded-lg text-sm font-bold transition {{ request('status') == 'suspended' ? 'bg-white text-red-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                موقوفين ({{ $suspendedCount }})
            </a>
        </div>
        
        <!-- Search Input -->
        <form action="{{ route('doctors.index') }}" method="GET" class="w-full md:w-[400px] relative">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث عن طبيب بالاسم، التخصص، الجوال..." class="w-full pl-4 pr-12 py-2.5 bg-gray-50 border-none rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-teal-custom/30 transition font-medium">
            <button type="submit" class="absolute inset-y-0 right-0 w-12 flex items-center justify-center text-gray-400 hover:text-teal-custom transition">
                <i class="fa-solid fa-search"></i>
            </button>
        </form>
    </div>

    <!-- Doctors Grid (Restoring the user preferred old card design) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($doctors as $doctor)
            @php
                $cardBg = 'bg-white';
                $cardBorder = 'border-gray-100';
                if ($doctor->status !== 'نشط') {
                    $cardBg = 'bg-white opacity-75 grayscale';
                    $cardBorder = 'border-red-200';
                } elseif (!$doctor->is_present) {
                    $cardBg = 'bg-orange-50/50';
                    $cardBorder = 'border-orange-200';
                }
            @endphp
            <div class="{{ $cardBg }} rounded-2xl shadow-sm border {{ $cardBorder }} p-6 relative group hover:-translate-y-1 transition duration-300">
                
                @if(!$doctor->is_present && $doctor->status === 'نشط')
                <!-- Background Icon for Vacation -->
                <div class="absolute inset-0 flex items-center justify-center opacity-[0.03] pointer-events-none overflow-hidden">
                    <i class="fa-solid fa-plane-departure text-9xl text-orange-900"></i>
                </div>
                @endif

                <!-- Options Dropdown -->
                <div class="absolute top-4 left-4 z-20" x-data="{ open: false }">
                    <button @click="open = !open" @click.away="open = false" class="text-gray-400 hover:text-teal-custom p-2 transition">
                        <i class="fa-solid fa-ellipsis-vertical"></i>
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div x-show="open" style="display: none;" class="absolute left-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-10">
                        <a href="{{ route('doctors.show', $doctor->id) }}" class="block px-4 py-2.5 text-sm font-bold text-gray-700 hover:bg-gray-50 flex items-center gap-2 transition">
                            <i class="fa-solid fa-eye text-teal-500 w-4"></i> عرض الملف
                        </a>
                        <button @click="open = false; openEditModal({{ $doctor }})" class="w-full text-right px-4 py-2.5 text-sm font-bold text-gray-700 hover:bg-gray-50 flex items-center gap-2 transition">
                            <i class="fa-solid fa-pen text-blue-500 w-4"></i> تعديل البيانات
                        </button>
                        <form action="{{ route('doctors.toggleStatus', $doctor->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="w-full text-right px-4 py-2.5 text-sm font-bold {{ $doctor->is_present ? 'text-orange-500 hover:bg-orange-50' : 'text-green-600 hover:bg-green-50' }} flex items-center gap-2 border-t border-gray-50 transition">
                                <i class="fa-solid {{ $doctor->is_present ? 'fa-plane-departure' : 'fa-clipboard-check' }} w-4"></i>
                                {{ $doctor->is_present ? 'تسجيل في إجازة' : 'تسجيل متاح الآن' }}
                            </button>
                        </form>
                        <form action="{{ route('doctors.toggleAccountStatus', $doctor->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="w-full text-right px-4 py-2.5 text-sm font-bold {{ $doctor->status === 'نشط' ? 'text-red-600 hover:bg-red-50' : 'text-blue-600 hover:bg-blue-50' }} flex items-center gap-2 border-t border-gray-50 transition">
                                <i class="fa-solid {{ $doctor->status === 'نشط' ? 'fa-user-lock' : 'fa-user-check' }} w-4"></i> 
                                {{ $doctor->status === 'نشط' ? 'إيقاف حساب الطبيب' : 'إعادة تفعيل الحساب' }}
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Status Badge Top Right (Fixed for user) -->
                @if($doctor->status !== 'نشط')
                <div class="absolute top-3 right-3 bg-red-500 text-white px-3 py-1 rounded-full text-[10px] font-bold flex items-center gap-1.5 shadow-md z-10">
                    <i class="fa-solid fa-lock"></i> حساب موقوف
                </div>
                @elseif(!$doctor->is_present)
                <div class="absolute top-3 right-3 bg-orange-500 text-white px-3 py-1 rounded-full text-[10px] font-bold flex items-center gap-1.5 shadow-md z-10 animate-pulse">
                    <i class="fa-solid fa-plane-departure"></i> إجازة رسمية
                </div>
                @endif

                <div class="text-center mt-2">
                    <!-- Avatar -->
                    <div class="relative w-24 h-24 mx-auto mb-4">
                        <div class="w-24 h-24 rounded-full bg-teal-50 border-4 border-white shadow-md flex items-center justify-center text-teal-600 text-3xl font-black overflow-hidden">
                            @if($doctor->avatar)
                                <img src="{{ asset('storage/' . $doctor->avatar) }}" class="w-full h-full object-cover">
                            @else
                                {{ mb_substr($doctor->name, 0, 1) }}
                            @endif
                        </div>
                        
                        <!-- Present/Absent Status Dot -->
                        <div class="absolute bottom-1 right-1 w-5 h-5 rounded-full border-4 border-white shadow-sm tooltip flex items-center justify-center
                            {{ $doctor->is_present ? 'bg-green-500 text-white' : 'bg-orange-500 text-white' }}"
                            title="{{ $doctor->is_present ? 'متواجد حالياً' : 'في إجازة/غائب' }}">
                        </div>
                    </div>

                    <h3 class="font-black text-lg text-gray-800 mb-1 {{ $doctor->status !== 'نشط' ? 'line-through text-gray-500' : '' }}">{{ $doctor->name }}</h3>
                    <p class="text-teal-600 font-bold text-sm mb-4">{{ $doctor->specialization }}</p>
                    
                    <div class="flex justify-center items-center gap-2 mb-4">
                        <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-xs font-bold border border-gray-200">
                            <i class="fa-solid fa-clinic-medical mr-1"></i> {{ $doctor->clinic->name ?? 'غير محدد' }}
                        </span>
                    </div>

                    <div class="pt-4 border-t border-gray-100 grid grid-cols-2 gap-2 text-center divide-x divide-x-reverse divide-gray-100">
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold mb-1 uppercase">رقم الجوال</p>
                            <p class="text-xs font-bold text-gray-700" dir="ltr">{{ $doctor->phone ?? 'غير متوفر' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold mb-1 uppercase">رقم الترخيص</p>
                            <p class="text-xs font-bold text-gray-700 font-mono">{{ $doctor->license_number ?? 'غير متوفر' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center bg-white rounded-2xl border border-gray-100">
                <i class="fa-solid fa-user-doctor text-5xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-bold text-gray-700">لا يوجد أطباء</h3>
                <p class="text-gray-500 text-sm mt-1">لم يتم العثور على أطباء يطابقون معايير البحث.</p>
            </div>
        @endforelse

        <!-- Add Doctor Card Button -->
        <div @click="openAddModal()" class="bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200 hover:border-teal-custom hover:bg-teal-50/50 p-6 flex flex-col items-center justify-center cursor-pointer transition group min-h-[300px]">
            <div class="w-16 h-16 rounded-full bg-white shadow-sm flex items-center justify-center text-teal-custom group-hover:scale-110 transition-transform mb-4">
                <i class="fa-solid fa-plus text-2xl"></i>
            </div>
            <span class="font-bold text-gray-600 group-hover:text-teal-700">إضافة طبيب جديد</span>
        </div>
    </div>

    @if($doctors->hasPages())
        <div class="flex justify-center mt-6">
            {{ $doctors->links() }}
        </div>
    @endif

    <!-- Add/Edit Modal -->
    <div x-show="isModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="isModalOpen" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" aria-hidden="true" @click="isModalOpen = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="isModalOpen" x-transition class="inline-block align-bottom bg-white rounded-2xl text-right overflow-visible shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
                
                <form :action="formAction" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div x-html="methodInput"></div>

                    <!-- Modal Header -->
                    <div class="bg-white px-6 py-4 border-b border-gray-100 flex justify-between items-center rounded-t-2xl">
                        <h3 class="text-lg font-black text-teal-custom" x-text="modalTitle"></h3>
                        <button type="button" @click="isModalOpen = false" class="text-gray-400 hover:text-gray-500 focus:outline-none w-8 h-8 flex items-center justify-center rounded-full bg-gray-50 hover:bg-gray-100">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="bg-white px-8 py-6 space-y-6 max-h-[70vh] overflow-y-auto">
                        
                        <!-- Avatar -->
                        <div x-data="{ photoName: null, photoPreview: null }" class="flex flex-col items-center justify-center bg-gray-50 p-5 rounded-2xl border border-gray-100">
                            <input type="file" class="hidden" x-ref="photo" name="avatar" accept="image/*"
                                x-on:change="
                                    photoName = $refs.photo.files[0].name;
                                    const reader = new FileReader();
                                    reader.onload = (e) => { photoPreview = e.target.result; };
                                    reader.readAsDataURL($refs.photo.files[0]);
                            ">
                            <label class="block text-sm font-bold text-gray-700 mb-3 text-center">صورة الطبيب الشخصية</label>
                            
                            <div class="mt-2" x-show="!photoPreview">
                                <div class="w-24 h-24 rounded-full bg-white border-2 border-dashed border-gray-300 flex items-center justify-center text-gray-400 cursor-pointer hover:border-teal-400 transition" @click.prevent="$refs.photo.click()">
                                    <i class="fa-solid fa-camera text-2xl"></i>
                                </div>
                            </div>

                            <div class="mt-2" x-show="photoPreview" style="display: none;">
                                <span class="block w-24 h-24 rounded-full shadow-md bg-cover bg-no-repeat bg-center cursor-pointer border-2 border-teal-500"
                                      x-bind:style="'background-image: url(\'' + photoPreview + '\');'"
                                      @click.prevent="$refs.photo.click()">
                                </span>
                            </div>
                            
                            <button type="button" class="mt-3 text-xs font-bold text-teal-600 bg-teal-50 px-3 py-1.5 rounded-lg" @click.prevent="$refs.photo.click()">رفع صورة</button>
                        </div>

                        <!-- Personal Data -->
                        <div>
                            <h4 class="text-sm font-bold text-teal-custom mb-4 border-b pb-2">البيانات الأساسية</h4>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-600 mb-2">اسم الطبيب <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" x-model="form.name" required class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:border-teal-custom font-bold">
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-600 mb-2">رقم الجوال</label>
                                        <input type="text" name="phone" x-model="form.phone" dir="ltr" class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:border-teal-custom text-right font-bold">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-600 mb-2">البريد الإلكتروني (اختياري)</label>
                                        <input type="email" name="email" x-model="form.email" dir="ltr" class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:border-teal-custom text-right font-bold">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Professional Data -->
                        <div>
                            <h4 class="text-sm font-bold text-teal-custom mb-4 border-b pb-2">البيانات المهنية والعيادة</h4>
                            <div class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-600 mb-2">التخصص الدقيق <span class="text-red-500">*</span></label>
                                        <input type="text" name="specialization" x-model="form.specialization" required class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:border-teal-custom font-bold">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-600 mb-2">القسم / العيادة <span class="text-red-500">*</span></label>
                                        <select name="clinic_id" x-model="form.clinic_id" required class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:border-teal-custom bg-white font-bold">
                                            <option value="" disabled selected>اختر العيادة...</option>
                                            @foreach($clinics as $clinic)
                                                <option value="{{ $clinic->id }}">{{ $clinic->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-600 mb-2">مدة الكشف (دقائق) <span class="text-red-500">*</span></label>
                                        <input type="number" name="consultation_duration" x-model="form.consultation_duration" required class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:border-teal-custom text-right font-bold">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-600 mb-2">رقم الترخيص</label>
                                        <input type="text" name="license_number" x-model="form.license_number" dir="ltr" class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:border-teal-custom text-right font-bold">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Modal Footer -->
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-end gap-3 rounded-b-2xl">
                        <button type="button" @click="isModalOpen = false" class="border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 px-6 py-2.5 rounded-xl text-sm font-bold transition">
                            إلغاء
                        </button>
                        <button type="submit" class="bg-teal-custom hover:bg-teal-700 text-white px-8 py-2.5 rounded-xl text-sm font-bold shadow-sm transition">
                            <i class="fa-solid fa-save mr-1"></i> حفظ بيانات الطبيب
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection

@section("scripts")
<script>
    window.doctorManager = function() {
        return {
            isModalOpen: false,
            modalTitle: 'إضافة طبيب جديد',
            formAction: '{{ route("doctors.store") }}',
            methodInput: '',
            form: {
                name: '', email: '', phone: '', clinic_id: '', specialization: '', license_number: '', consultation_duration: 30
            },
            openAddModal() {
                this.modalTitle = 'إضافة طبيب جديد';
                this.formAction = '{{ route("doctors.store") }}';
                this.methodInput = '';
                this.form = { name: '', email: '', phone: '', clinic_id: '', specialization: '', license_number: '', consultation_duration: 30 };
                this.isModalOpen = true;
            },
            openEditModal(doctor) {
                this.modalTitle = 'تعديل بيانات الطبيب: ' + doctor.name;
                this.formAction = `/doctors/${doctor.id}`;
                this.methodInput = '<input type="hidden" name="_method" value="PUT">';
                this.form = { 
                    name: doctor.name, 
                    email: doctor.email, 
                    phone: doctor.phone || '', 
                    clinic_id: doctor.clinic_id, 
                    specialization: doctor.specialization, 
                    license_number: doctor.license_number || '', 
                    consultation_duration: doctor.consultation_duration 
                };
                this.isModalOpen = true;
            }
        };
    };
</script>
@endsection
