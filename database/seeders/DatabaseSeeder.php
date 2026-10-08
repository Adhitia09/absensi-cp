<?php
namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(['email' => 'admin@dapurkita.test'], ['name' => 'Admin AbsensiResto', 'username' => 'admin', 'password' => 'password', 'role' => 'admin']);
    }
}
