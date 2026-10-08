@extends('layouts.app')

@section('title', 'الخدمات الطبية والأسعار - نظام العيادة')
@section('header', 'دليل الخدمات الطبية والأسعار')

@section('content')
<div class="space-y-6" x-data="servicesManager()">

    <!-- Header & Action -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center bg-white p-6 rounded-3xl shadow-sm border border-gray-100 gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-2xl shadow-sm">
                <i class="fa-solid fa-tags"></i>
            </div>
            <div>
                <h2 class="text-xl font-black text-gray-800">قائمة الخدمات الطبية</h2>
                <p class="text-sm text-gray-500 font-bold mt-1">إدارة الكشفيات، التحاليل، الإجراءات الطبية وأسعارها.</p>
            </div>
        </div>
        <button @click="openAddModal()" class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl text-sm font-bold shadow-sm transition flex items-center justify-center gap-2">
            <i class="fa-solid fa-plus"></i> إضافة خدمة جديدة
        </button>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-r-4 border-green-500 text-green-700 p-4 rounded-xl flex items-center gap-3 font-bold shadow-sm">
            <i class="fa-solid fa-circle-check text-xl"></i>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <!-- Services Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($services as $service)
        <div class="bg-white rounded-2xl shadow-sm border {{ $service->is_active ? 'border-gray-100 hover:border-blue-200' : 'border-red-100 bg-red-50/30' }} p-5 transition group relative overflow-hidden flex flex-col justify-between">
            
            @if(!$service->is_active)
            <div class="absolute top-0 right-0 bg-red-500 text-white text-[10px] font-black px-3 py-1 rounded-bl-lg">موقوف</div>
            @endif

            <div>
                <div class="flex justify-between items-start mb-3">
                    <h3 class="font-black text-gray-800 text-lg leading-tight">{{ $service->name }}</h3>
                </div>
                <div class="mb-4">
                    <span class="inline-block bg-gray-100 text-gray-600 text-xs font-bold px-2.5 py-1 rounded-md border border-gray-200">
                        {{ $service->category ?? 'عام' }}
                    </span>
                </div>
                @if($service->description)
                <p class="text-xs font-bold text-gray-500 line-clamp-2 mb-4">{{ $service->description }}</p>
                @endif
            </div>

            <div class="mt-auto border-t border-gray-100 pt-4 flex justify-between items-center">
                <div class="text-blue-600 font-black text-lg font-mono" dir="ltr">
                    {{ number_format($service->price, 2) }} <span class="text-xs text-gray-400">{{ $global_settings['currency'] ?? 'SAR' }}</span>
                </div>
                
                <div class="flex gap-2">
                    <button @click="openEditModal({{ json_encode($service) }})" class="w-8 h-8 rounded-lg bg-gray-50 hover:bg-blue-50 text-gray-400 hover:text-blue-600 border border-gray-200 transition flex items-center justify-center">
                        <i class="fa-solid fa-pen text-xs"></i>
                    </button>
                    <form action="{{ route('services.destroy', $service->id) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه الخدمة؟');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-8 h-8 rounded-lg bg-gray-50 hover:bg-red-50 text-gray-400 hover:text-red-600 border border-gray-200 transition flex items-center justify-center">
                            <i class="fa-solid fa-trash text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white p-12 rounded-3xl border border-gray-100 text-center shadow-sm">
            <i class="fa-solid fa-tags text-6xl text-gray-200 mb-4 block"></i>
            <h3 class="text-xl font-black text-gray-700 mb-2">لا توجد خدمات طبية</h3>
            <p class="text-gray-500 font-bold mb-6">قم بإضافة الكشفيات والخدمات لكي تتمكن من إصدار الفواتير للمرضى.</p>
            <button @click="openAddModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-xl text-sm font-bold shadow-sm transition inline-flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> إضافة الخدمة الأولى
            </button>
        </div>
        @endforelse
    </div>

    <!-- Add/Edit Modal -->
    <div x-show="isModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-900/60 backdrop-blur-sm p-4">
        <div x-show="isModalOpen" x-transition.opacity class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl p-6 md:p-8 border border-gray-100" @click.away="isModalOpen = false">
            <div class="flex justify-between items-center mb-6 border-b border-gray-50 pb-4">
                <h3 class="text-xl font-black text-gray-800" x-text="modalTitle"></h3>
                <button @click="isModalOpen = false" class="text-gray-400 hover:text-red-500 transition">
                    <i class="fa-solid fa-xmark text-2xl"></i>
                </button>
            </div>

            <form :action="formAction" method="POST">
                @csrf
                <div x-html="methodInput"></div>

                <div class="space-y-5">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">اسم الخدمة <span class="text-red-500">*</span></label>
                        <input type="text" name="name" x-model="form.name" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white rounded-xl text-sm font-bold transition outline-none">
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">السعر <span class="text-red-500">*</span></label>
                            <input type="number" step="0.01" name="price" x-model="form.price" required dir="ltr" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white rounded-xl text-sm font-bold text-right transition outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">التصنيف</label>
                            <select name="category" x-model="form.category" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white rounded-xl text-sm font-bold transition outline-none appearance-none">
                                <option value="كشفية">كشفية</option>
                                <option value="إجراء طبي">إجراء طبي</option>
                                <option value="مختبر وتحاليل">مختبر وتحاليل</option>
                                <option value="أشعة">أشعة</option>
                                <option value="أخرى">أخرى</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">وصف الخدمة (اختياري)</label>
                        <textarea name="description" x-model="form.description" rows="2" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white rounded-xl text-sm font-bold transition outline-none"></textarea>
                    </div>

                    <label class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-200 cursor-pointer hover:bg-gray-100 transition">
                        <input type="checkbox" name="is_active" value="1" x-model="form.is_active" class="w-5 h-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                        <span class="text-sm font-black text-gray-700">الخدمة نشطة ومتاحة للاستخدام</span>
                    </label>
                </div>

                <div class="mt-8 flex gap-3">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl text-sm font-black shadow-lg shadow-blue-500/30 transition">
                        حفظ البيانات
                    </button>
                    <button type="button" @click="isModalOpen = false" class="px-6 py-3 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 rounded-xl text-sm font-bold transition">
                        إلغاء
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
function servicesManager() {
    return {
        isModalOpen: false,
        modalTitle: '',
        formAction: '',
        methodInput: '',
        form: {
            name: '',
            price: '',
            category: 'كشفية',
            description: '',
            is_active: true
        },
        openAddModal() {
            this.modalTitle = 'إضافة خدمة طبية جديدة';
            this.formAction = '{{ route('services.store') }}';
            this.methodInput = '';
            this.form = { name: '', price: '', category: 'كشفية', description: '', is_active: true };
            this.isModalOpen = true;
        },
        openEditModal(service) {
            this.modalTitle = 'تعديل الخدمة: ' + service.name;
            this.formAction = `/services/${service.id}`;
            this.methodInput = '<input type="hidden" name="_method" value="PUT">';
            this.form = {
                name: service.name,
                price: service.price,
                category: service.category || 'كشفية',
                description: service.description || '',
                is_active: service.is_active
            };
            this.isModalOpen = true;
        }
    }
}
</script>
@endsection
