@extends('layouts.app')

@section('title', 'شركات التأمين الطبي - Clinc')
@section('header', 'إدارة شركات التأمين والتعاقدات (Insurance Companies)')

@section('content')
<div class="space-y-6" x-data="{ showAddModal: false, showEditModal: false, activeCompany: {} }">

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
        <div>
            <h2 class="text-xl font-black text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-building-shield text-teal-600"></i>
                شركات التأمين الطبي المعتمدة
            </h2>
            <p class="text-xs text-gray-500 font-bold mt-1">إدارة التعاقدات ونسب الخصم والربط مع وثائق المرضى ومطالبات التأمين</p>
        </div>

        <div class="flex items-center gap-2.5 w-full sm:w-auto">
            <a href="{{ route('insurance-claims.index') }}" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 hover:bg-gray-100 text-gray-700 text-xs font-bold transition">
                <i class="fa-solid fa-file-invoice-dollar"></i>
                لوحة المطالبات التأمينية
            </a>
            <button @click="showAddModal = true" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold shadow-md shadow-teal-600/20 transition">
                <i class="fa-solid fa-plus"></i>
                إضافة شركة تأمين جديدة
            </button>
        </div>
    </div>

    <!-- Search -->
    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
        <form method="GET" action="{{ route('insurance-companies.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث باسم شركة التأمين أو الرمز أو جهة الاتصال..." class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:border-teal-500">
            </div>
            <button type="submit" class="px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl transition">
                <i class="fa-solid fa-magnifying-glass ml-1"></i> بحث
            </button>
            @if(request('search'))
                <a href="{{ route('insurance-companies.index') }}" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-bold rounded-xl transition">إلغاء</a>
            @endif
        </form>
    </div>

    <!-- Companies Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($companies as $company)
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm hover:border-teal-200 transition flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-start mb-3">
                    <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center font-bold text-sm">
                        {{ mb_substr($company->name, 0, 1) }}
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $company->is_active ? 'bg-green-100 text-green-700' : 'bg-rose-100 text-rose-700' }}">
                        {{ $company->is_active ? 'تعاقد نشط' : 'معطل' }}
                    </span>
                </div>

                <h3 class="font-black text-gray-800 text-sm mb-1">{{ $company->name }}</h3>
                <span class="inline-block px-2 py-0.5 rounded bg-gray-100 font-mono text-gray-600 text-[10px] font-bold mb-3">{{ $company->code }}</span>

                <div class="space-y-1.5 text-xs text-gray-600 border-t border-gray-50 pt-3">
                    <p class="flex items-center gap-2"><i class="fa-solid fa-user-tie text-gray-400 w-4"></i> {{ $company->contact_person ?? '—' }}</p>
                    <p class="flex items-center gap-2"><i class="fa-solid fa-phone text-gray-400 w-4"></i> <span dir="ltr">{{ $company->phone ?? '—' }}</span></p>
                    <p class="flex items-center gap-2"><i class="fa-solid fa-percent text-teal-600 w-4"></i> نسبة الخصم المتفق عليها: <strong class="font-mono text-teal-700">{{ $company->discount_percentage }}%</strong></p>
                </div>
            </div>

            <div class="pt-4 mt-4 border-t border-gray-100 flex items-center justify-between text-xs">
                <span class="text-gray-400 font-bold">
                    <strong class="text-gray-700 font-mono">{{ $company->patient_insurances_count }}</strong> مريض مؤمّن
                </span>

                <div class="flex items-center gap-1.5">
                    <button type="button" @click="activeCompany = {{ json_encode($company) }}; showEditModal = true" class="p-1.5 rounded-lg bg-teal-50 hover:bg-teal-100 text-teal-700 transition" title="تعديل">
                        <i class="fa-solid fa-pen text-xs"></i>
                    </button>
                    <form action="{{ route('insurance-companies.destroy', $company->id) }}" method="POST" onsubmit="return confirm('تأكيد حذف شركة التأمين هذه؟')" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 transition" title="حذف">
                            <i class="fa-solid fa-trash-can text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white rounded-2xl p-12 text-center text-gray-400">
            <i class="fa-solid fa-shield-halved text-4xl mb-3 text-gray-300"></i>
            <p class="font-bold text-sm">لا توجد شركات تأمين مسجلة</p>
        </div>
        @endforelse
    </div>

    <!-- Add Company Modal -->
    <div x-show="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-gray-900/50 backdrop-blur-sm p-4" style="display: none;">
        <div @click.away="showAddModal = false" class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden relative transform transition-all">
            <div class="bg-teal-600 p-5 text-white flex justify-between items-center">
                <h3 class="font-black text-base flex items-center gap-2"><i class="fa-solid fa-building-shield"></i> إضافة شركة تأمين جديدة</h3>
                <button @click="showAddModal = false" class="text-white/70 hover:text-white transition"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <form action="{{ route('insurance-companies.store') }}" method="POST" class="p-6 space-y-3.5">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">اسم شركة التأمين <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" required placeholder="مثال: بوبا العربية للتأمين" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:border-teal-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">الرمز / الكود <span class="text-rose-500">*</span></label>
                        <input type="text" name="code" required placeholder="BUPA" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:border-teal-500 uppercase font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">مسؤول الاتصال / القسم</label>
                        <input type="text" name="contact_person" placeholder="قسم الموافقات الطبية" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:border-teal-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">رقم الهاتف</label>
                        <input type="text" name="phone" placeholder="800..." class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:border-teal-500 font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">البريد الإلكتروني</label>
                        <input type="email" name="email" placeholder="claims@..." class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:border-teal-500 font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">نسبة الخصم المعتمدة (%)</label>
                        <input type="number" step="0.01" min="0" max="100" name="discount_percentage" value="0.00" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:border-teal-500 font-mono">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">العنوان أو المقر</label>
                    <input type="text" name="address" placeholder="المدينة، الحي..." class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:border-teal-500">
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" checked id="is_active_comp" class="rounded text-teal-600 focus:ring-teal-500">
                    <label for="is_active_comp" class="text-xs font-bold text-gray-700 cursor-pointer">التعاقد ساري ومفعل</label>
                </div>

                <div class="flex items-center gap-2 pt-2 border-t border-gray-100">
                    <button type="submit" class="flex-1 bg-teal-600 hover:bg-teal-700 text-white py-2.5 rounded-xl text-xs font-bold transition">حفظ الشركة</button>
                    <button type="button" @click="showAddModal = false" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold transition">إلغاء</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Company Modal -->
    <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-gray-900/50 backdrop-blur-sm p-4" style="display: none;">
        <div @click.away="showEditModal = false" class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden relative transform transition-all">
            <div class="bg-teal-700 p-5 text-white flex justify-between items-center">
                <h3 class="font-black text-base flex items-center gap-2"><i class="fa-solid fa-pen-to-square"></i> تعديل بيانات شركة التأمين</h3>
                <button @click="showEditModal = false" class="text-white/70 hover:text-white transition"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <form :action="'{{ url('insurance-companies') }}/' + activeCompany.id" method="POST" class="p-6 space-y-3.5">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">اسم شركة التأمين</label>
                        <input type="text" name="name" x-model="activeCompany.name" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:border-teal-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">الرمز / الكود</label>
                        <input type="text" name="code" x-model="activeCompany.code" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:border-teal-500 uppercase font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">مسؤول الاتصال</label>
                        <input type="text" name="contact_person" x-model="activeCompany.contact_person" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:border-teal-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">رقم الهاتف</label>
                        <input type="text" name="phone" x-model="activeCompany.phone" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:border-teal-500 font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">البريد الإلكتروني</label>
                        <input type="email" name="email" x-model="activeCompany.email" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:border-teal-500 font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">نسبة الخصم (%)</label>
                        <input type="number" step="0.01" min="0" max="100" name="discount_percentage" x-model="activeCompany.discount_percentage" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:border-teal-500 font-mono">
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" :checked="activeCompany.is_active" id="is_active_comp_edit" class="rounded text-teal-600 focus:ring-teal-500">
                    <label for="is_active_comp_edit" class="text-xs font-bold text-gray-700 cursor-pointer">التعاقد ساري ومفعل</label>
                </div>

                <div class="flex items-center gap-2 pt-2 border-t border-gray-100">
                    <button type="submit" class="flex-1 bg-teal-600 hover:bg-teal-700 text-white py-2.5 rounded-xl text-xs font-bold transition">حفظ التعديلات</button>
                    <button type="button" @click="showEditModal = false" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold transition">إلغاء</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
