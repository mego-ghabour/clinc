@extends('layouts.app')

@section('title', 'العيادات والأقسام - نظام العيادة')
@section('header', 'إدارة العيادات والأقسام الطبية')

@section('content')
<div x-data="clinicManager()">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-teal-custom mb-1">العيادات والأقسام</h1>
            <p class="text-gray-500 text-sm">
                {{ $clinics->whereNull('deleted_at')->count() }} عيادة مسجلة - 
                {{ $clinics->whereNull('deleted_at')->where('status', 'نشطة')->count() }} نشطة، 
                {{ $clinics->whereNull('deleted_at')->where('status', 'موقوفة')->count() }} موقوفة
            </p>
        </div>
        <button @click="openAddModal()" class="w-full md:w-auto bg-teal-custom hover:bg-teal-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm flex items-center justify-center gap-2">
            <i class="fa-solid fa-plus"></i>
            إضافة عيادة جديدة
        </button>
    </div>

    @if($errors->any())
        <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 shadow-sm border border-red-100 font-bold">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Filters -->
    <div class="flex flex-wrap items-center gap-3 mb-6">
        <button @click="filterStatus = 'all'" :class="filterStatus === 'all' ? 'bg-teal-custom text-white' : 'bg-white border border-gray-200 text-gray-600'" class="px-4 py-1.5 rounded-full text-sm font-bold transition shadow-sm">الكل ({{ $clinics->whereNull('deleted_at')->count() }})</button>
        <button @click="filterStatus = 'نشطة'" :class="filterStatus === 'نشطة' ? 'bg-teal-custom text-white' : 'bg-white border border-gray-200 text-gray-600'" class="px-4 py-1.5 rounded-full text-sm font-bold transition shadow-sm">نشطة ({{ $clinics->whereNull('deleted_at')->where('status', 'نشطة')->count() }})</button>
        <button @click="filterStatus = 'موقوفة'" :class="filterStatus === 'موقوفة' ? 'bg-teal-custom text-white' : 'bg-white border border-gray-200 text-gray-600'" class="px-4 py-1.5 rounded-full text-sm font-bold transition shadow-sm">موقوفة ({{ $clinics->whereNull('deleted_at')->where('status', 'موقوفة')->count() }})</button>
        <button @click="filterStatus = 'trashed'" :class="filterStatus === 'trashed' ? 'bg-red-500 text-white border-transparent' : 'bg-white border border-red-200 text-red-500 hover:bg-red-50'" class="px-4 py-1.5 rounded-full text-sm font-bold transition shadow-sm">سلة المحذوفات ({{ $clinics->whereNotNull('deleted_at')->count() }})</button>
        
        <div class="flex-1 min-w-[200px]">
            <div class="relative">
                <input type="text" x-model="searchQuery" placeholder="ابحث باسم العيادة أو الكود..." class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:border-teal-custom text-sm bg-white shadow-sm transition">
                <i class="fa-solid fa-search absolute left-4 top-2.5 text-gray-400"></i>
            </div>
        </div>
    </div>

    <!-- Clinics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($clinics as $clinic)
        <div class="bg-white rounded-2xl p-5 border {{ $clinic->trashed() ? 'border-red-200 bg-red-50/20' : 'border-gray-100' }} shadow-sm hover:shadow-md transition group relative overflow-hidden flex flex-col h-full"
             x-show="matchesSearch({{ json_encode($clinic) }}) && matchesFilter('{{ $clinic->trashed() ? 'trashed' : $clinic->status }}')">
            
            <div class="absolute top-0 right-0 h-1 w-full" style="background-color: {{ $clinic->color }}"></div>

            <div class="flex justify-between items-start mb-4 mt-2">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl shadow-sm border" style="background-color: {{ $clinic->color }}15; color: {{ $clinic->color }}; border-color: {{ $clinic->color }}30">
                    @if($clinic->icon && \Storage::disk('public')->exists($clinic->icon))
                        <img src="{{ asset('storage/' . $clinic->icon) }}" class="w-8 h-8 object-contain">
                    @else
                        <i class="fa-solid fa-stethoscope"></i>
                    @endif
                </div>
                
                @if($clinic->trashed())
                    <span class="bg-red-100 text-red-700 px-2.5 py-0.5 rounded text-xs font-bold border border-red-200">مؤرشفة</span>
                @else
                    <span class="{{ $clinic->status === 'نشطة' ? 'bg-green-100 text-green-700 border-green-200' : 'bg-gray-100 text-gray-600 border-gray-200' }} px-2.5 py-0.5 rounded text-xs font-bold border">
                        {{ $clinic->status }}
                    </span>
                @endif
            </div>

            <div class="flex-1">
                <h3 class="font-bold text-gray-800 text-lg mb-1 leading-tight"><a href="{{ route('clinics.show', $clinic->id) }}" class="hover:text-teal-custom">{{ $clinic->name }}</a></h3>
                <p class="text-xs text-gray-400 font-mono mb-4">{{ $clinic->code }}</p>
                
                @if($clinic->headDoctor)
                <div class="flex items-center gap-2 mt-4 bg-gray-50 p-2 rounded-lg border border-gray-100">
                    <div class="w-6 h-6 rounded-full bg-teal-100 text-teal-600 flex items-center justify-center text-[10px]">
                        @if($clinic->headDoctor->avatar)
                            <img src="{{ asset('storage/' . $clinic->headDoctor->avatar) }}" class="w-full h-full rounded-full object-cover">
                        @else
                            <i class="fa-solid fa-user-md"></i>
                        @endif
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold leading-none">رئيس القسم</p>
                        <p class="text-xs font-bold text-gray-700">د. {{ $clinic->headDoctor->name }}</p>
                    </div>
                </div>
                @endif
                
                <div class="flex gap-4 mt-4 text-xs font-bold text-gray-500">
                    <div class="flex items-center gap-1.5" title="عدد الأطباء في هذه العيادة">
                        <i class="fa-solid fa-user-md text-gray-400"></i> {{ $clinic->doctors->count() }} أطباء
                    </div>
                    @if($clinic->start_time && $clinic->end_time)
                    <div class="flex items-center gap-1.5" title="ساعات العمل">
                        <i class="fa-regular fa-clock text-gray-400"></i> 
                        <span dir="ltr">{{ \Carbon\Carbon::parse($clinic->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($clinic->end_time)->format('h:i A') }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <div class="border-t border-gray-100 pt-3 mt-4 flex items-center justify-between">
                <a href="{{ route('clinics.show', $clinic->id) }}" class="text-xs font-bold text-teal-600 hover:text-teal-800 transition">
                    التفاصيل <i class="fa-solid fa-arrow-left mr-1"></i>
                </a>
                <div class="flex items-center gap-1">
                    @if(!$clinic->trashed())
                        <button @click="openEditModal({{ json_encode($clinic) }})" class="w-8 h-8 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 flex items-center justify-center transition border border-transparent hover:border-blue-100" title="تعديل">
                            <i class="fa-solid fa-pen text-xs"></i>
                        </button>
                        <form action="{{ route('clinics.destroy', $clinic->id) }}" method="POST" class="inline" onsubmit="return confirm('هل تريد بالتأكيد أرشفة هذه العيادة؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-8 h-8 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 flex items-center justify-center transition border border-transparent hover:border-red-100" title="أرشفة">
                                <i class="fa-solid fa-trash text-xs"></i>
                            </button>
                        </form>
                    @else
                        <form action="{{ route('clinics.restore', $clinic->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="w-8 h-8 rounded-lg text-gray-400 hover:text-green-600 hover:bg-green-50 flex items-center justify-center transition border border-transparent hover:border-green-100" title="استعادة">
                                <i class="fa-solid fa-arrow-rotate-left text-xs"></i>
                            </button>
                        </form>
                        <form action="{{ route('clinics.destroy', $clinic->id) }}" method="POST" class="inline" onsubmit="return confirm('تحذير: سيتم حذف العيادة نهائياً ولا يمكن التراجع. هل أنت متأكد؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-8 h-8 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 flex items-center justify-center transition border border-transparent hover:border-red-100" title="حذف نهائي">
                                <i class="fa-solid fa-ban text-xs"></i>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white p-12 rounded-3xl border border-gray-100 text-center shadow-sm">
            <i class="fa-solid fa-door-open text-6xl text-gray-200 mb-4 block"></i>
            <h3 class="text-xl font-black text-gray-700 mb-2">لا توجد عيادات مسجلة</h3>
            <p class="text-gray-500 font-bold mb-6">قم بإضافة العيادات والأقسام الطبية لربطها بالأطباء والمواعيد.</p>
            <button @click="openAddModal()" class="bg-teal-custom hover:bg-teal-700 text-white px-8 py-3 rounded-xl text-sm font-bold shadow-sm transition inline-flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> إضافة العيادة الأولى
            </button>
        </div>
        @endforelse
    </div>

    <!-- Modal Form -->
    <div x-show="isModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm overflow-y-auto">
        <div x-show="isModalOpen" x-transition.opacity class="bg-white rounded-3xl shadow-xl w-full max-w-2xl flex flex-col overflow-hidden" @click.away="closeModal()">
            
            <div class="bg-teal-50 px-6 py-4 border-b border-teal-100 flex justify-between items-center">
                <h3 class="text-xl font-black text-teal-900" x-text="modalTitle"></h3>
                <button @click="closeModal()" class="text-teal-400 hover:text-teal-600 transition">
                    <i class="fa-solid fa-xmark text-2xl"></i>
                </button>
            </div>

            <div class="p-6">
                <form :action="formAction" method="POST" enctype="multipart/form-data" id="clinicForm">
                    @csrf
                    <div x-html="methodInput"></div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">اسم العيادة <span class="text-red-500">*</span></label>
                            <input type="text" name="name" x-model="form.name" required placeholder="مثال: عيادة الأسنان" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:border-teal-500 focus:bg-white rounded-xl text-sm font-bold transition outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">كود العيادة (اختياري)</label>
                            <input type="text" name="code" x-model="form.code" dir="ltr" placeholder="DEN-01" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:border-teal-500 focus:bg-white rounded-xl text-sm font-bold text-right transition outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">لون التمييز (Hex)</label>
                            <div class="flex items-center gap-2">
                                <input type="color" name="color" x-model="form.color" class="h-11 w-14 rounded-xl border border-gray-200 cursor-pointer">
                                <input type="text" x-model="form.color" dir="ltr" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:border-teal-500 focus:bg-white rounded-xl text-sm font-bold transition outline-none text-right">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">الحالة <span class="text-red-500">*</span></label>
                            <select name="status" x-model="form.status" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:border-teal-500 focus:bg-white rounded-xl text-sm font-bold transition outline-none appearance-none">
                                <option value="نشطة">نشطة</option>
                                <option value="موقوفة">موقوفة</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">أيقونة العيادة (صورة)</label>
                            <input type="file" name="icon_file" accept="image/*" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 focus:border-teal-500 rounded-xl text-sm font-bold transition outline-none file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100">
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-5 mt-5">
                        <h4 class="text-sm font-black text-gray-800 mb-4">إعدادات إضافية</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">وقت بدء العمل</label>
                                <input type="time" name="start_time" x-model="form.start_time" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:border-teal-500 focus:bg-white rounded-xl text-sm font-bold transition outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">وقت انتهاء العمل</label>
                                <input type="time" name="end_time" x-model="form.end_time" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:border-teal-500 focus:bg-white rounded-xl text-sm font-bold transition outline-none">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">وصف أو ملاحظات</label>
                                <textarea name="notes" x-model="form.notes" rows="2" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:border-teal-500 focus:bg-white rounded-xl text-sm font-bold transition outline-none"></textarea>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-end gap-3 mt-auto">
                <button type="button" @click="closeModal()" class="px-6 py-3 bg-white border border-gray-200 hover:bg-gray-100 text-gray-700 rounded-xl text-sm font-bold transition">
                    إلغاء
                </button>
                <button type="submit" form="clinicForm" class="px-6 py-3 bg-teal-custom hover:bg-teal-700 text-white rounded-xl text-sm font-black shadow-lg shadow-teal-500/30 transition flex items-center gap-2">
                    <i class="fa-solid fa-save"></i> حفظ البيانات
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function clinicManager() {
    return {
        searchQuery: '',
        filterStatus: 'all',
        isModalOpen: false,
        modalTitle: '',
        formAction: '',
        methodInput: '',
        form: {
            name: '',
            code: '',
            color: '#14b8a6',
            status: 'نشطة',
            start_time: '',
            end_time: '',
            notes: ''
        },
        
        matchesSearch(clinic) {
            if (this.searchQuery === '') return true;
            const q = this.searchQuery.toLowerCase();
            return (
                (clinic.name && clinic.name.toLowerCase().includes(q)) ||
                (clinic.code && clinic.code.toLowerCase().includes(q))
            );
        },
        
        matchesFilter(status) {
            if (this.filterStatus === 'all') return status !== 'trashed';
            return this.filterStatus === status;
        },

        openAddModal() {
            this.modalTitle = 'إضافة عيادة جديدة';
            this.formAction = '{{ route('clinics.store') }}';
            this.methodInput = '';
            this.form = {
                name: '', code: '', color: '#14b8a6', status: 'نشطة', start_time: '', end_time: '', notes: ''
            };
            this.isModalOpen = true;
        },

        openEditModal(clinic) {
            this.modalTitle = 'تعديل بيانات العيادة';
            this.formAction = `/clinics/${clinic.id}`;
            this.methodInput = '<input type="hidden" name="_method" value="PUT">';
            
            this.form = {
                name: clinic.name || '',
                code: clinic.code || '',
                color: clinic.color || '#14b8a6',
                status: clinic.status || 'نشطة',
                start_time: clinic.start_time ? clinic.start_time.substring(0, 5) : '',
                end_time: clinic.end_time ? clinic.end_time.substring(0, 5) : '',
                notes: clinic.notes || ''
            };
            this.isModalOpen = true;
        },

        closeModal() {
            this.isModalOpen = false;
            setTimeout(() => {
                document.getElementById('clinicForm').reset();
            }, 300);
        }
    }
}
</script>
@endsection
