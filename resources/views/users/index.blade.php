@extends('layouts.app')

@section('title', 'إدارة المستخدمين والموظفين')
@section('header', 'الأطباء والموظفين')

@section('content')
<div x-data="userManager()" class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-teal-50 text-teal-600 rounded-xl flex items-center justify-center text-2xl shadow-sm border border-teal-100">
                <i class="fa-solid fa-user-doctor"></i>
            </div>
            <div>
                <h1 class="text-2xl font-black text-gray-800 mb-1">الأطباء والفريق الطبي</h1>
                <p class="text-gray-500 text-sm font-bold">إدارة حسابات الأطباء، الممرضين، والموظفين في العيادة</p>
            </div>
        </div>
        <button @click="openAddModal()" class="bg-teal-600 hover:bg-teal-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold transition shadow-sm flex items-center gap-2 w-full md:w-auto justify-center">
            <i class="fa-solid fa-user-plus"></i> إضافة موظف جديد
        </button>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-r-4 border-green-500 text-green-700 p-4 rounded-xl flex items-center gap-3 shadow-sm" x-data="{ show: true }" x-show="show">
            <i class="fa-solid fa-circle-check text-xl"></i>
            <p class="font-bold text-sm">{{ session('success') }}</p>
            <button @click="show = false" class="mr-auto hover:text-green-900"><i class="fa-solid fa-xmark"></i></button>
        </div>
    @endif
    @if($errors->any())
        <div class="bg-red-50 border-r-4 border-red-500 text-red-700 p-4 rounded-xl flex items-center gap-3 shadow-sm">
            <i class="fa-solid fa-circle-exclamation text-xl"></i>
            <p class="font-bold text-sm">{{ $errors->first() }}</p>
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white p-4 rounded-3xl shadow-sm border border-gray-100">
        <form action="{{ route('system-users.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-center">
            
            <div class="flex-1 w-full relative">
                <i class="fa-solid fa-search absolute right-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث بالاسم، البريد، أو معرف الدخول..." class="w-full pr-11 pl-4 py-2.5 bg-gray-50 border border-gray-200 focus:border-teal-500 focus:bg-white rounded-xl text-sm transition font-bold">
            </div>

            <div class="w-full md:w-64">
                <select name="role" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 focus:border-teal-500 focus:bg-white rounded-xl text-sm transition font-bold text-gray-700 cursor-pointer" onchange="this.form.submit()">
                    <option value="all">كل الأدوار الوظيفية</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>
                            {{ $role->name === 'Super Admin' ? 'مدير النظام' : $role->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <!-- Users Table (Responsive) -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-right whitespace-nowrap">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-6 py-4 text-xs font-black text-gray-500 uppercase tracking-wider">الموظف</th>
                        <th class="px-6 py-4 text-xs font-black text-gray-500 uppercase tracking-wider">بيانات الدخول</th>
                        <th class="px-6 py-4 text-xs font-black text-gray-500 uppercase tracking-wider">المنصب / الصلاحية</th>
                        <th class="px-6 py-4 text-xs font-black text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-4 text-xs font-black text-gray-500 uppercase tracking-wider text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-xl {{ $user->status === 'نشط' ? 'bg-teal-50 text-teal-600 border-teal-100' : 'bg-gray-100 text-gray-400 border-gray-200 grayscale' }} flex items-center justify-center font-black text-xl shrink-0 overflow-hidden border">
                                    @if($user->avatar)
                                        <img src="{{ asset('storage/' . $user->avatar) }}" class="w-full h-full object-cover">
                                    @else
                                        {{ mb_substr($user->name, 0, 1) }}
                                    @endif
                                </div>
                                <div>
                                    <h3 class="font-black text-gray-800 text-sm {{ $user->status !== 'نشط' ? 'line-through text-gray-400' : '' }}">{{ $user->name }}</h3>
                                    <p class="text-xs text-gray-500 font-bold mt-0.5" dir="ltr">{{ $user->phone ?? 'لا يوجد رقم' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-xs font-bold text-gray-600 space-y-1">
                                <div><i class="fa-solid fa-envelope text-gray-400 ml-1"></i> <span dir="ltr">{{ $user->email }}</span></div>
                                <div><i class="fa-solid fa-user-tag text-gray-400 ml-1"></i> <span dir="ltr">{{ $user->username }}</span></div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1">
                                @forelse($user->roles as $role)
                                    <span class="text-[10px] {{ $role->name === 'Super Admin' ? 'bg-purple-100 text-purple-700 border-purple-200' : 'bg-blue-50 text-blue-700 border-blue-100' }} px-2.5 py-1 rounded-md font-black border">
                                        {{ $role->name === 'Super Admin' ? 'مدير عام 👑' : $role->name }}
                                    </span>
                                @empty
                                    <span class="text-[10px] bg-gray-100 text-gray-500 px-2.5 py-1 rounded-md border border-gray-200 font-bold">مستخدم عادي</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($user->status === 'نشط')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[10px] font-black bg-green-50 text-green-700 border border-green-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> نشط
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[10px] font-black bg-red-50 text-red-700 border border-red-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> موقوف
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('system-users.show', $user->id) }}" class="w-8 h-8 rounded-lg bg-gray-50 text-gray-600 hover:bg-gray-200 hover:text-gray-900 flex items-center justify-center transition tooltip" title="الملف الشامل">
                                    <i class="fa-solid fa-eye text-sm"></i>
                                </a>
                                <button @click='openEditModal(@json($user), @json($user->roles->pluck("name")))' class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 flex items-center justify-center transition tooltip" title="تعديل">
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </button>
                                
                                @if($user->id !== auth()->id())
                                <form action="{{ route('system_users.toggleStatus', $user->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="w-8 h-8 rounded-lg {{ $user->status === 'نشط' ? 'bg-orange-50 text-orange-600 hover:bg-orange-100' : 'bg-green-50 text-green-600 hover:bg-green-100' }} flex items-center justify-center transition tooltip" title="{{ $user->status === 'نشط' ? 'إيقاف الحساب' : 'تفعيل الحساب' }}">
                                        <i class="fa-solid {{ $user->status === 'نشط' ? 'fa-lock' : 'fa-lock-open' }} text-sm"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="w-16 h-16 bg-gray-50 text-gray-300 rounded-full flex items-center justify-center text-2xl mx-auto mb-3">
                                <i class="fa-solid fa-users-slash"></i>
                            </div>
                            <p class="text-sm font-bold text-gray-500">لا يوجد مستخدمين أو أطباء مضافين.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($users->hasPages())
        <div class="bg-gray-50 border-t border-gray-100 p-4">
            {{ $users->links() }}
        </div>
        @endif
    </div>

    <!-- Modal (Add/Edit User) -->
    <div x-show="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-gray-900/50 backdrop-blur-sm p-4" style="display: none;" x-transition>
        <div @click.away="isModalOpen = false" class="bg-white rounded-3xl shadow-2xl w-full max-w-2xl overflow-hidden relative">
            <div class="bg-teal-600 p-6 text-white flex justify-between items-center">
                <h3 class="font-black text-lg" x-text="modalTitle"></h3>
                <button @click="isModalOpen = false" class="text-white/70 hover:text-white transition"><i class="fa-solid fa-xmark text-xl"></i></button>
            </div>
            
            <div class="max-h-[75vh] overflow-y-auto custom-scrollbar">
                <form :action="formAction" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div x-html="methodInput"></div>
                    
                    <div class="p-6 md:p-8 space-y-6">
                        
                        <!-- Personal Info -->
                        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                            <h4 class="font-black text-gray-700 mb-4 flex items-center gap-2 text-sm border-b border-gray-50 pb-3">
                                <i class="fa-solid fa-user text-teal-500"></i> البيانات الشخصية
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">الاسم الكامل <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" x-model="form.name" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 focus:border-teal-500 focus:bg-white rounded-xl text-sm font-bold transition">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">رقم الجوال</label>
                                    <input type="text" name="phone" x-model="form.phone" dir="ltr" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 focus:border-teal-500 focus:bg-white rounded-xl text-sm text-right font-bold transition">
                                </div>
                            </div>
                        </div>

                        <!-- Auth Info -->
                        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                            <h4 class="font-black text-gray-700 mb-4 flex items-center gap-2 text-sm border-b border-gray-50 pb-3">
                                <i class="fa-solid fa-key text-blue-500"></i> بيانات الدخول
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">البريد الإلكتروني <span class="text-red-500">*</span></label>
                                    <input type="email" name="email" x-model="form.email" required dir="ltr" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 focus:border-teal-500 focus:bg-white rounded-xl text-sm text-right font-bold transition">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">معرف الدخول (Username) <span class="text-red-500">*</span></label>
                                    <input type="text" name="username" x-model="form.username" required dir="ltr" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 focus:border-teal-500 focus:bg-white rounded-xl text-sm text-right font-bold transition">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">كلمة المرور <span x-show="!isEditMode" class="text-red-500">*</span></label>
                                    <input type="password" name="password" :required="!isEditMode" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 focus:border-teal-500 focus:bg-white rounded-xl text-sm font-bold transition" :placeholder="isEditMode ? 'اتركه فارغاً إذا لا تريد التغيير' : '********'">
                                </div>
                            </div>
                        </div>

                        <!-- Roles -->
                        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                            <h4 class="font-black text-gray-700 mb-2 flex items-center gap-2 text-sm">
                                <i class="fa-solid fa-shield-halved text-purple-500"></i> الأدوار الوظيفية <span class="text-red-500">*</span>
                            </h4>
                            <p class="text-xs text-gray-500 mb-4 font-bold">حدد دوراً واحداً أو أكثر للموظف لمنحه صلاحيات محددة مسبقاً.</p>
                            
                            <div class="grid grid-cols-2 gap-3">
                                @foreach($roles as $role)
                                    <label class="flex items-center p-3 bg-gray-50 border border-gray-200 rounded-xl cursor-pointer hover:border-teal-400 transition group relative overflow-hidden">
                                        <input type="checkbox" name="roles[]" value="{{ $role->name }}" x-model="form.roles" class="w-4 h-4 text-teal-600 rounded border-gray-300 focus:ring-teal-500 relative z-10">
                                        <div class="mr-3 text-sm flex-1 relative z-10">
                                            <span class="font-bold text-gray-700 group-hover:text-gray-900 transition">
                                                {{ $role->name === 'Super Admin' ? 'مدير النظام' : $role->name }}
                                            </span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Dynamic Doctor Fields -->
                        <div x-show="form.roles.includes('طبيب')" x-transition.opacity class="bg-blue-50/50 p-5 rounded-2xl border border-blue-100 shadow-sm" style="display: none;">
                            <h4 class="font-black text-blue-800 mb-4 flex items-center gap-2 text-sm border-b border-blue-100 pb-3">
                                <i class="fa-solid fa-user-md text-blue-500"></i> بيانات الطبيب التخصصية (تظهر للأطباء فقط)
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">تعيين في عيادة <span class="text-red-500">*</span></label>
                                    <select name="clinic_id" x-model="form.clinic_id" :required="form.roles.includes('طبيب')" class="w-full px-4 py-2.5 bg-white border border-blue-200 focus:border-blue-500 rounded-xl text-sm font-bold transition">
                                        <option value="">اختر العيادة...</option>
                                        @foreach($clinics as $clinic)
                                            <option value="{{ $clinic->id }}">{{ $clinic->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">التخصص الطبي <span class="text-red-500">*</span></label>
                                    <input type="text" name="specialization" x-model="form.specialization" :required="form.roles.includes('طبيب')" placeholder="مثال: باطنة، أسنان..." class="w-full px-4 py-2.5 bg-white border border-blue-200 focus:border-blue-500 rounded-xl text-sm font-bold transition">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">رقم الرخصة (License) <span class="text-red-500">*</span></label>
                                    <input type="text" name="license_number" x-model="form.license_number" :required="form.roles.includes('طبيب')" dir="ltr" class="w-full px-4 py-2.5 bg-white border border-blue-200 focus:border-blue-500 rounded-xl text-sm text-right font-bold transition">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">مدة الكشف الافتراضية (بالدقائق) <span class="text-red-500">*</span></label>
                                    <input type="number" name="consultation_duration" x-model="form.consultation_duration" :required="form.roles.includes('طبيب')" min="5" placeholder="20" dir="ltr" class="w-full px-4 py-2.5 bg-white border border-blue-200 focus:border-blue-500 rounded-xl text-sm text-right font-bold transition">
                                    <p class="text-[10px] text-gray-400 font-bold mt-1">يستخدم هذا الرقم لتقسيم المواعيد في الاستقبال.</p>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Modal Footer -->
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-end gap-3 items-center">
                        <button type="button" @click="isModalOpen = false" class="bg-white border border-gray-200 hover:bg-gray-100 text-gray-700 px-6 py-2.5 rounded-xl text-sm font-bold transition">
                            إلغاء
                        </button>
                        <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-8 py-2.5 rounded-xl text-sm font-bold shadow-sm transition flex items-center gap-2">
                            <i class="fa-solid fa-save"></i> حفظ المهام
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
    window.userManager = function() {
        return {
            isModalOpen: false,
            isEditMode: false,
            modalTitle: 'إضافة موظف جديد',
            formAction: '{{ route('system-users.store') }}',
            methodInput: '',
            form: {
                name: '',
                email: '',
                username: '',
                phone: '',
                roles: [],
                clinic_id: '',
                specialization: '',
                license_number: '',
                consultation_duration: 20
            },

            openAddModal() {
                this.isEditMode = false;
                this.modalTitle = 'إضافة طبيب أو موظف جديد';
                this.formAction = '{{ route('system-users.store') }}';
                this.methodInput = '';
                this.form = { name: '', email: '', username: '', phone: '', roles: [], clinic_id: '', specialization: '', license_number: '', consultation_duration: 20 };
                this.isModalOpen = true;
            },

            openEditModal(user, userRoles) {
                this.isEditMode = true;
                this.modalTitle = 'تعديل بيانات: ' + user.name;
                this.formAction = `/system-users/${user.id}`;
                this.methodInput = '<input type="hidden" name="_method" value="PUT">';
                this.form = { 
                    name: user.name, 
                    email: user.email, 
                    username: user.username, 
                    phone: user.phone || '',
                    roles: userRoles,
                    clinic_id: user.clinic_id || '',
                    specialization: user.specialization || '',
                    license_number: user.license_number || '',
                    consultation_duration: user.consultation_duration || 20
                };
                this.isModalOpen = true;
            }
        };
    };
</script>
@endsection
