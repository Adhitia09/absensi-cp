<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('name');
        });
        foreach (DB::table('users')->select('id', 'email')->get() as $user) {
            $base = preg_replace('/[^A-Za-z0-9_-]/', '_', explode('@', $user->email)[0]) ?: 'user';
            $username = $base;
            $suffix = 1;
            while (DB::table('users')->where('username', $username)->exists()) {
                $username = $base.'_'.$suffix++;
            }
            DB::table('users')->where('id', $user->id)->update(['username' => $username]);
        }
    }
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) { $table->dropUnique(['username']); $table->dropColumn('username'); });
    }
};
