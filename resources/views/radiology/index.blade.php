@extends('layouts.app')

@section('title', 'قسم الأشعة والمختبرات - Clinc')
@section('header', 'لوحة تحكم قسم الأشعة (Radiology Dept)')

@section('content')
<div class="space-y-6">

    <!-- Header & Action -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-2xl shadow-sm border border-indigo-100">
                <i class="fa-solid fa-x-ray"></i>
            </div>
            <div>
                <h2 class="text-xl font-black text-gray-800">طابور طلبات الأشعة (المرضى بالخارج)</h2>
                <p class="text-sm text-gray-500 font-bold mt-1">استقبال طلبات الأطباء، إرفاق الصور والتقارير.</p>
            </div>
        </div>
        <div class="bg-indigo-100 text-indigo-800 px-4 py-2 rounded-xl text-sm font-black flex items-center gap-2">
            <span class="relative flex h-3 w-3">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-indigo-500"></span>
            </span>
            النظام يعمل في الوقت الفعلي
        </div>
    </div>

    <!-- Requests Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($requests as $request)
        <div class="bg-white rounded-3xl shadow-sm border {{ $request->status == 'pending' ? 'border-orange-200 hover:border-indigo-300' : 'border-gray-100' }} p-5 transition group flex flex-col relative overflow-hidden">
            
            @if($request->status == 'pending')
                <div class="absolute top-0 right-0 w-16 h-16 bg-orange-50 rounded-bl-full -mr-8 -mt-8 opacity-50 z-0"></div>
                <div class="absolute top-3 right-3 z-10 flex items-center gap-1 bg-orange-100 text-orange-700 px-2 py-1 rounded text-[10px] font-black">
                    <i class="fa-solid fa-clock"></i> في الانتظار
                </div>
            @else
                <div class="absolute top-3 right-3 z-10 flex items-center gap-1 bg-green-100 text-green-700 px-2 py-1 rounded text-[10px] font-black">
                    <i class="fa-solid fa-check-double"></i> مكتمل
                </div>
            @endif

            <div class="mt-4 relative z-10">
                <h3 class="font-black text-gray-800 text-lg mb-1 truncate"><a href="{{ route('patients.show', $request->patient_id) }}" class="hover:text-indigo-600 transition">{{ $request->patient->name }}</a></h3>
                <div class="text-xs text-gray-500 font-mono mb-4">MRN: {{ $request->patient->mrn }}</div>
                
                <div class="space-y-2 mb-4 bg-gray-50 p-3 rounded-2xl border border-gray-100">
                    <div class="flex justify-between items-center text-sm">
                        <span class="font-bold text-gray-500">نوع الأشعة:</span>
                        <span class="font-black text-indigo-700">{{ $request->type }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="font-bold text-gray-500">الجزء المراد تصويره:</span>
                        <span class="font-bold text-gray-800">{{ $request->body_part ?? 'غير محدد' }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm border-t border-gray-200 pt-2 mt-2">
                        <span class="font-bold text-gray-500 text-[10px]">الطبيب الطالب:</span>
                        <span class="font-bold text-gray-600 text-xs">د. {{ $request->doctor->name }}</span>
                    </div>
                </div>
                
                @if($request->clinical_notes)
                <div class="mb-4">
                    <p class="text-[10px] font-bold text-red-500 uppercase mb-1">ملاحظات الطبيب (Clinical Notes)</p>
                    <p class="text-xs font-bold text-gray-600 line-clamp-2 bg-red-50 p-2 rounded-lg">{{ $request->clinical_notes }}</p>
                </div>
                @endif
            </div>

            <!-- Action -->
            <div class="mt-auto pt-4 border-t border-gray-100 relative z-10">
                <a href="{{ route('radiology.show', $request->id) }}" class="w-full text-center block {{ $request->status == 'pending' ? 'bg-indigo-600 hover:bg-indigo-700 text-white' : 'bg-gray-100 hover:bg-gray-200 text-gray-700' }} py-2.5 rounded-xl text-sm font-bold shadow-sm transition">
                    @if($request->status == 'pending')
                        <i class="fa-solid fa-camera mr-1"></i> تصوير وإرفاق التقرير
                    @else
                        <i class="fa-solid fa-eye mr-1"></i> عرض التقرير
                    @endif
                </a>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white rounded-3xl shadow-sm border border-gray-100 p-16 text-center">
            <div class="w-24 h-24 bg-gray-50 text-gray-300 rounded-full flex items-center justify-center text-5xl mb-6 mx-auto">
                <i class="fa-solid fa-mug-hot"></i>
            </div>
            <h2 class="text-xl font-black text-gray-700 mb-2">طابور الأشعة فارغ حالياً</h2>
            <p class="text-gray-500 max-w-md mx-auto text-sm font-bold">
                لا توجد طلبات أشعة معلقة من الأطباء في الوقت الحالي. يمكنك أخذ استراحة قصيرة!
            </p>
        </div>
        @endforelse
    </div>

    @if($requests->hasPages())
    <div class="bg-gray-50 border-t border-gray-100 p-4 rounded-b-3xl">
        {{ $requests->links() }}
    </div>
    @endif

</div>
@endsection
