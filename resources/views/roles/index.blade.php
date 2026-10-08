@extends('layouts.app')

@section('title', 'الأدوار والصلاحيات')
@section('header', 'الأدوار والصلاحيات (RBAC)')

@section('content')
<div x-data="roleManager()">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 mb-1">الأدوار والصلاحيات</h1>
            <p class="text-gray-500 text-sm">إدارة مجموعات الصلاحيات والمسميات الوظيفية</p>
        </div>
        <button @click="openAddModal()" class="bg-teal-custom hover:bg-teal-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold transition shadow-sm flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> إضافة دور جديد
        </button>
    </div>

    <!-- Roles Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        @foreach($roles as $role)
            @php 
                $isSuperAdmin = $role->name === 'Super Admin';
                $displayName = $isSuperAdmin ? 'مسئول النظام' : $role->name;
            @endphp
            <div class="bg-white rounded-2xl shadow-sm border {{ $isSuperAdmin ? 'border-teal-300 bg-teal-50/20' : 'border-gray-100' }} p-6 relative group overflow-hidden transition hover:shadow-md flex flex-col h-full">
                
                @if($isSuperAdmin)
                    <div class="absolute top-0 right-0 w-16 h-16 bg-teal-100 rounded-bl-full -z-10"></div>
                @endif

                <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl {{ $isSuperAdmin ? 'bg-teal-100 text-teal-600' : 'bg-gray-50 text-gray-500' }} flex items-center justify-center text-xl font-bold shrink-0">
                            <i class="fa-solid {{ $isSuperAdmin ? 'fa-crown' : 'fa-user-shield' }}"></i>
                        </div>
                        <div>
                            <h3 class="font-black text-lg text-gray-800">{{ $displayName }}</h3>
                            @if($isSuperAdmin)
                                <span class="text-[10px] bg-teal-600 text-white px-2 py-0.5 rounded font-mono block mt-1 w-fit">Super Admin</span>
                            @endif
                        </div>
                    </div>
                    
                    @if(!$isSuperAdmin)
                        <!-- Actions -->
                        <div class="flex gap-1 transition">
                            <button @click="openEditModal({{ $role }}, {{ $role->permissions->pluck('name') }})" class="w-8 h-8 rounded-lg bg-gray-50 text-blue-600 hover:bg-blue-50 flex items-center justify-center transition" title="تعديل الدور">
                                <i class="fa-solid fa-pen text-xs"></i>
                            </button>
                            <form action="{{ route('roles.destroy', $role->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الدور؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-8 h-8 rounded-lg bg-gray-50 text-red-600 hover:bg-red-50 flex items-center justify-center transition" title="حذف الدور">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    @endif
                </div>

                <div class="flex-1 mt-2">
                    <p class="text-xs text-gray-400 font-bold uppercase mb-2">أبرز الصلاحيات الممنوحة</p>
                    <div class="flex flex-wrap gap-1.5">
                        @if($isSuperAdmin)
                            <span class="bg-gray-100 text-gray-600 px-2.5 py-1.5 rounded-lg text-xs font-bold border border-gray-200 w-full text-center">يملك جميع الصلاحيات في النظام بلا قيود</span>
                        @else
                            @forelse($role->permissions->take(6) as $perm)
                                <span class="bg-gray-50 border border-gray-100 text-gray-600 px-2 py-1 rounded text-[10px] font-bold">
                                    {{ trans('permissions.' . $perm->name) === 'permissions.' . $perm->name ? $perm->name : trans('permissions.' . $perm->name) }}
                                </span>
                            @empty
                                <span class="text-gray-400 text-xs italic">لا توجد صلاحيات مخصصة</span>
                            @endforelse
                            
                            @if($role->permissions->count() > 6)
                                <span class="bg-teal-50 border border-teal-100 text-teal-600 px-2 py-1 rounded text-[10px] font-bold">
                                    +{{ $role->permissions->count() - 6 }} إضافية
                                </span>
                            @endif
                        @endif
                    </div>
                </div>
                
                <div class="mt-6 pt-4 border-t border-gray-50 flex justify-between items-center text-xs text-gray-500 font-bold">
                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-users text-gray-400"></i> {{ $role->users()->count() }} مستخدم</span>
                    <span>تم التحديث: {{ $role->updated_at->format('Y-m-d') }}</span>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Add/Edit Modal -->
    <div x-show="isModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Overlay -->
            <div x-show="isModalOpen" x-transition.opacity class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true" @click="isModalOpen = false"></div>
            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <!-- Modal Content -->
            <div x-show="isModalOpen" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 class="inline-block align-bottom bg-gray-50 rounded-2xl text-right overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl w-full">
                
                <form :action="formAction" method="POST">
                    @csrf
                    <div x-html="methodInput"></div>
                    
                    <!-- Modal Header -->
                    <div class="bg-white px-6 py-5 border-b border-gray-100 flex justify-between items-center sticky top-0 z-10">
                        <h3 class="text-xl font-black text-gray-800 flex items-center gap-2">
                            <span class="w-8 h-8 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center text-sm">
                                <i class="fa-solid fa-shield"></i>
                            </span>
                            <span x-text="modalTitle"></span>
                        </h3>
                        <button type="button" @click="isModalOpen = false" class="w-8 h-8 rounded-full bg-gray-50 text-gray-400 hover:bg-gray-100 hover:text-gray-600 focus:outline-none transition flex items-center justify-center">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="px-6 py-6 max-h-[65vh] overflow-y-auto custom-scrollbar">
                        
                        <div class="mb-8">
                            <label class="block text-sm font-bold text-gray-700 mb-2">اسم الدور الوظيفي <span class="text-red-500">*</span></label>
                            <input type="text" name="name" x-model="form.name" required placeholder="مثال: موظف استقبال، طبيب أسنان، إلخ..." class="w-full px-4 py-3 bg-white border border-gray-200 focus:border-teal-custom rounded-xl text-sm focus:outline-none focus:ring-4 focus:ring-teal-custom/10 transition font-bold text-gray-800">
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-4">
                                <label class="block text-sm font-bold text-gray-700">الصلاحيات الممنوحة <span class="text-red-500">*</span></label>
                                <button type="button" @click="toggleAllPermissions()" class="text-xs font-bold text-teal-600 hover:text-teal-800 transition">تحديد الكل / إلغاء الكل</button>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @php
                                    // Group permissions by prefix
                                    $groupedPermissions = $permissions->groupBy(function($perm) {
                                        return explode('_', $perm->name)[1] ?? 'other';
                                    });
                                @endphp

                                @foreach($groupedPermissions as $group => $perms)
                                <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                                    <h4 class="font-bold text-gray-800 text-sm mb-3 pb-2 border-b border-gray-50 flex items-center gap-2 uppercase">
                                        <i class="fa-solid fa-layer-group text-teal-500"></i> قسم {{ trans('permissions.module_'.$group) === 'permissions.module_'.$group ? $group : trans('permissions.module_'.$group) }}
                                    </h4>
                                    <div class="space-y-2">
                                        @foreach($perms as $perm)
                                        <label class="flex items-center group cursor-pointer p-1.5 rounded-lg hover:bg-gray-50 transition">
                                            <input type="checkbox" name="permissions[]" value="{{ $perm->name }}" x-model="form.permissions" class="w-4 h-4 text-teal-600 rounded border-gray-300 focus:ring-teal-500 permission-checkbox transition">
                                            <div class="mr-3 text-sm flex-1">
                                                <span class="font-bold text-gray-600 group-hover:text-gray-900 transition block">
                                                    {{ trans('permissions.' . $perm->name) === 'permissions.' . $perm->name ? $perm->name : trans('permissions.' . $perm->name) }}
                                                </span>
                                                <span class="text-[10px] text-gray-400 font-mono" dir="ltr">{{ $perm->name }}</span>
                                            </div>
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="bg-white px-6 py-4 border-t border-gray-100 flex justify-end gap-3 items-center">
                        <button type="button" @click="isModalOpen = false" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-xl text-sm font-bold transition">
                            إلغاء
                        </button>
                        <button type="submit" class="bg-teal-custom hover:bg-teal-700 text-white px-8 py-2.5 rounded-xl text-sm font-bold shadow-sm hover:shadow-md transition flex items-center gap-2">
                            <i class="fa-solid fa-save"></i> حفظ الدور والصلاحيات
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
    window.roleManager = function() {
        return {
            isModalOpen: false,
            modalTitle: 'إضافة دور جديد',
            formAction: '{{ route('roles.store') }}',
            methodInput: '',
            form: {
                name: '',
                permissions: []
            },

            openAddModal() {
                this.modalTitle = 'إنشاء مسمى وظيفي جديد';
                this.formAction = '{{ route('roles.store') }}';
                this.methodInput = '';
                this.form = { name: '', permissions: [] };
                this.isModalOpen = true;
            },

            openEditModal(role, rolePermissions) {
                this.modalTitle = 'تعديل الصلاحيات: ' + (role.name === 'Super Admin' ? 'مسئول النظام' : role.name);
                this.formAction = `/roles/${role.id}`;
                this.methodInput = '<input type="hidden" name="_method" value="PUT">';
                this.form = { name: role.name, permissions: rolePermissions };
                this.isModalOpen = true;
            },
            
            toggleAllPermissions() {
                const checkboxes = document.querySelectorAll('.permission-checkbox');
                const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                
                if (allChecked) {
                    this.form.permissions = [];
                } else {
                    const allPerms = Array.from(checkboxes).map(cb => cb.value);
                    this.form.permissions = allPerms;
                }
            }
        };
    };
</script>
@endsection
