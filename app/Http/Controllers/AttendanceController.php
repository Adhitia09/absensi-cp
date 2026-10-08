<?php
namespace App\Http\Controllers;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
class AttendanceController extends Controller
{
    public function checkIn(Request $request)
    {
        $data = $request->validate(['latitude' => ['required', 'numeric', 'between:-90,90'], 'longitude' => ['required', 'numeric', 'between:-180,180']], ['latitude.required' => 'Lokasi belum terbaca. Izinkan akses lokasi lalu coba lagi.', 'longitude.required' => 'Lokasi belum terbaca. Izinkan akses lokasi lalu coba lagi.']);
        $attendance = Attendance::firstOrCreate(['user_id' => auth()->id(), 'date' => Carbon::today()], ['status' => 'Hadir']);
        if ($attendance->check_in) return back()->with('info', 'Kamu sudah melakukan absen masuk hari ini.');
        $now = now(); $attendance->update(['check_in' => $now, 'check_in_latitude' => $data['latitude'], 'check_in_longitude' => $data['longitude'], 'status' => $now->format('H:i') > '08:00' ? 'Terlambat' : 'Hadir']);
        return back()->with('success', 'Absen masuk berhasil dicatat pada '.$now->format('H:i').'.');
    }
    public function checkOut(Request $request)
    {
        $data = $request->validate(['latitude' => ['required', 'numeric', 'between:-90,90'], 'longitude' => ['required', 'numeric', 'between:-180,180']], ['latitude.required' => 'Lokasi belum terbaca. Izinkan akses lokasi lalu coba lagi.', 'longitude.required' => 'Lokasi belum terbaca. Izinkan akses lokasi lalu coba lagi.']);
        $attendance = Attendance::where('user_id', auth()->id())->whereDate('date', Carbon::today())->first();
        if (! $attendance || ! $attendance->check_in) return back()->with('error', 'Lakukan absen masuk terlebih dahulu.');
        if ($attendance->check_out) return back()->with('info', 'Kamu sudah melakukan absen pulang hari ini.');
        $attendance->update(['check_out' => now(), 'check_out_latitude' => $data['latitude'], 'check_out_longitude' => $data['longitude']]); return back()->with('success', 'Absen pulang berhasil dicatat. Hati-hati di jalan!');
    }
}
