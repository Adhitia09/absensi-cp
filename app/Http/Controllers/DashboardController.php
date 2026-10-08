<?php
namespace App\Http\Controllers;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Support\Carbon;
class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $user = auth()->user();
        $isAdmin = $user->role === 'admin';
        $attendanceQuery = Attendance::query();
        if (! $isAdmin) {
            $attendanceQuery->where('user_id', $user->id);
        }
        return view('dashboard', [
            'isAdmin' => $isAdmin,
            'attendance' => $isAdmin ? null : Attendance::where('user_id', $user->id)->whereDate('date', $today)->first(),
            'todayCount' => (clone $attendanceQuery)->whereDate('date', $today)->count(),
            'employeeCount' => $isAdmin ? User::where('role', 'pegawai')->count() : 1,
            'presentCount' => (clone $attendanceQuery)->whereDate('date', $today)->whereIn('status', ['Hadir', 'Terlambat'])->count(),
            'recentAttendances' => (clone $attendanceQuery)->with('user')->latest('date')->latest('check_in')->limit(8)->get(),
            'pendingLeaveCount' => $isAdmin ? LeaveRequest::where('status', 'Menunggu')->count() : 0,
        ]);
    }
}
