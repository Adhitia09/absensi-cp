<?php
namespace App\Http\Controllers;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
class AttendanceExportController extends Controller
{
    public function export(Request $request)
    {
        $month=$request->input('month',now()->format('Y-m')); if (!preg_match('/^\d{4}-\d{2}$/',$month)) $month=now()->format('Y-m');
        $date=Carbon::createFromFormat('Y-m',$month); $query=Attendance::with('user')->whereBetween('date',[$date->copy()->startOfMonth(),$date->copy()->endOfMonth()])->whereHas('user',fn($q)=>$q->where('role','pegawai'));
        if(auth()->user()->role!=='admin') $query->where('user_id',auth()->id()); $rows=$query->orderBy('date')->orderBy('user_id')->get();
        return response()->streamDownload(function() use($rows){ $out=fopen('php://output','w'); fputcsv($out,['Tanggal','Nama Pegawai','Email','Jam Masuk','Jam Pulang','Status']); foreach($rows as $row) fputcsv($out,[$row->date->format('Y-m-d'),$row->user->name,$row->user->email,$row->check_in?->format('H:i'),$row->check_out?->format('H:i'),$row->status]); fclose($out); },'absensi-'.$month.'.csv',['Content-Type'=>'text/csv']);
    }
}
