<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'all');
        $query = Activity::with(['causer', 'subject'])->latest();

        if ($tab === 'auth') {
            $query->whereIn('event', ['login', 'logout']);
        } else {
            $query->whereNotIn('event', ['login', 'logout']);
        }

        // General Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', '%' . $search . '%')
                  ->orWhere('event', 'like', '%' . $search . '%')
                  ->orWhereHas('causer', function($causerQ) use ($search) {
                      $causerQ->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        // Advanced Filters
        if ($request->filled('event_type') && $request->event_type !== 'all') {
            $query->where('event', $request->event_type);
        }

        if ($request->filled('subject_type') && $request->subject_type !== 'all') {
            $query->where('subject_type', 'like', '%' . $request->subject_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(20)->withQueryString();

        return view('audit_logs.index', compact('logs', 'tab'));
    }

    public function rollback($id)
    {
        $activity = Activity::findOrFail($id);
        $subject = $activity->subject;

        if (!$subject) {
            $modelClass = $activity->subject_type;
            if (method_exists($modelClass, 'withTrashed')) {
                $subject = $modelClass::withTrashed()->find($activity->subject_id);
            }
        }

        if (!$subject) {
            return back()->withErrors(['error' => 'لا يمكن التراجع. السجل الأصلي غير موجود كلياً في قاعدة البيانات.']);
        }

        if ($activity->event === 'updated') {
            $oldProps = $activity->properties['old'] ?? null;
            if ($oldProps) {
                activity()->disableLogging();
                $subject->update($oldProps);
                activity()->enableLogging();
                
                activity()
                    ->performedOn($subject)
                    ->causedBy(auth()->user())
                    ->event('rollback')
                    ->log('تم استعادة القيم القديمة عبر نظام التراجع');

                return back()->with('success', 'تم التراجع عن التعديل واستعادة القيم السابقة بنجاح.');
            }
        }

        if ($activity->event === 'deleted') {
            if (method_exists($subject, 'restore')) {
                activity()->disableLogging();
                $subject->restore();
                activity()->enableLogging();

                activity()
                    ->performedOn($subject)
                    ->causedBy(auth()->user())
                    ->event('rollback')
                    ->log('تم استعادة السجل المحذوف');

                return back()->with('success', 'تم استعادة السجل المحذوف بنجاح.');
            }
        }

        return back()->withErrors(['error' => 'لا يمكن التراجع عن هذه العملية تحديداً.']);
    }
}
