<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class LeaveRequest extends Model
{
    protected $fillable = ['user_id','start_date','end_date','type','reason','status','admin_note','approved_by'];
    protected function casts(): array { return ['start_date'=>'date','end_date'=>'date']; }
    public function user() { return $this->belongsTo(User::class); }
    public function approver() { return $this->belongsTo(User::class, 'approved_by'); }
}
