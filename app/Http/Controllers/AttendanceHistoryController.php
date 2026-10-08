<?php
namespace App\Http\Controllers;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
class AttendanceHistoryController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->role === 'admin', 403);
        $query = Attendance::with('user')->whereHas('user', fn ($q) => $q->where('role', 'pegawai'))->latest('date')->latest('check_in');
        if ($request->filled('employee')) $query->where('user_id', $request->integer('employee'));
        if ($request->filled('from')) $query->whereDate('date', '>=', $request->date('from'));
        if ($request->filled('to')) $query->whereDate('date', '<=', $request->date('to'));
        if ($request->filled('status')) $query->where('status', $request->string('status'));
        return view('attendance.history', ['attendances' => $query->paginate(15)->withQueryString(), 'employees' => User::where('role','pegawai')->orderBy('name')->get()]);
    }
}
