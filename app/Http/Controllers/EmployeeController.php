<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
class EmployeeController extends Controller
{
    private function ensureAdmin(): void { abort_unless(auth()->user()->role === 'admin', 403); }
    public function index() { $this->ensureAdmin(); return view('employees.index', ['employees' => User::where('role', 'pegawai')->latest()->get()]); }
    public function store(Request $request)
    {
        $this->ensureAdmin();
        $data = $request->validate(['name' => ['required','string','max:100'], 'username' => ['required','string','alpha_dash','max:50','unique:users,username'], 'email' => ['required','email','max:150','unique:users,email'], 'password' => ['required','string','min:6']]);
        User::create(['name' => $data['name'], 'username' => $data['username'], 'email' => $data['email'], 'password' => Hash::make($data['password']), 'role' => 'pegawai']);
        return back()->with('success', 'Pegawai berhasil ditambahkan.');
    }
    public function destroy(User $user)
    {
        $this->ensureAdmin(); abort_if($user->role !== 'pegawai', 403);
        $user->delete(); return back()->with('success', 'Data pegawai berhasil dihapus.');
    }

    public function resetPassword(User $user)
    {
        $this->ensureAdmin();
        abort_if($user->role !== 'pegawai', 403);

        $temporaryPassword = Str::password(10);
        $user->update(['password' => Hash::make($temporaryPassword)]);

        return back()->with('success', 'Password '.$user->name.' berhasil direset.')
            ->with('temporary_password', $temporaryPassword);
    }
}
