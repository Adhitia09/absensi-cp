<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Attendance extends Model
{
    protected $fillable = ['user_id', 'date', 'check_in', 'check_out', 'check_in_latitude', 'check_in_longitude', 'check_out_latitude', 'check_out_longitude', 'status', 'note'];
    protected function casts(): array { return ['date' => 'date', 'check_in' => 'datetime', 'check_out' => 'datetime']; }
    public function user() { return $this->belongsTo(User::class); }
}
