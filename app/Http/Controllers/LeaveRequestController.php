<?php
namespace App\Http\Controllers;
use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Http\Request;
class LeaveRequestController extends Controller
{
    public function index() { $admin=auth()->user()->role==='admin'; return view('leaves.index',['isAdmin'=>$admin,'requests'=>($admin?LeaveRequest::with('user')->latest():LeaveRequest::where('user_id',auth()->id())->latest())->paginate(12)->withQueryString()]); }
    public function store(Request $request)
    {
        $data=$request->validate(['start_date'=>['required','date'],'end_date'=>['required','date','after_or_equal:start_date'],'type'=>['required','in:Cuti,Sakit,Izin'],'reason'=>['required','string','max:500']]);
        LeaveRequest::create($data+['user_id'=>auth()->id(),'status'=>'Menunggu']); return back()->with('success','Pengajuan izin berhasil dikirim ke admin.');
    }
    public function update(Request $request, LeaveRequest $leaveRequest)
    {
        abort_unless(auth()->user()->role==='admin',403); $data=$request->validate(['status'=>['required','in:Disetujui,Ditolak'],'admin_note'=>['nullable','string','max:500']]);
        $leaveRequest->update($data+['approved_by'=>auth()->id()]); return back()->with('success','Status pengajuan izin berhasil diperbarui.');
    }
}
