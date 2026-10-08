@extends('layouts.app')
@section('content')
<div class="content">
    <div class="headline">
        <div><div class="eyebrow">Akun saya</div><h1>Profil</h1></div>
        <div class="date">{{ now()->translatedFormat('l, d F Y') }}</div>
    </div>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-error">{{ session('error') }}</div>@endif
    @if($errors->any())<div class="alert alert-error">{{ $errors->first() }}</div>@endif

    <div class="profile-grid">
        <section class="panel profile-card">
            <div class="profile-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div class="profile-name"><h2>{{ auth()->user()->name }}</h2><span class="pill">{{ auth()->user()->role === 'admin' ? 'Administrator' : 'Pegawai' }}</span></div>
            <div class="profile-details">
                <div><span>Username</span><strong>{{ auth()->user()->username }}</strong></div>
                <div><span>Email</span><strong>{{ auth()->user()->email }}</strong></div>
                <div><span>Bergabung sejak</span><strong>{{ auth()->user()->created_at->translatedFormat('d F Y') }}</strong></div>
            </div>
        </section>

        <section class="panel">
            <div class="panel-head"><h2>Reset password</h2></div>
            <p class="muted profile-intro">Perbarui password akun secara berkala. Password lama diperlukan untuk memastikan perubahan ini dilakukan oleh pemilik akun.</p>
            <form method="POST" action="{{ route('profile.password.update') }}" class="profile-form">
                @csrf @method('PUT')
                <div class="field"><label for="current_password">Password lama</label><input id="current_password" type="password" name="current_password" required autocomplete="current-password"></div>
                <div class="field"><label for="password">Password baru</label><input id="password" type="password" name="password" minlength="6" required autocomplete="new-password"><small class="muted">Minimal 6 karakter.</small></div>
                <div class="field"><label for="password_confirmation">Konfirmasi password baru</label><input id="password_confirmation" type="password" name="password_confirmation" minlength="6" required autocomplete="new-password"></div>
                <button class="btn btn-primary">Simpan password baru</button>
            </form>
        </section>
    </div>
</div>
@endsection
<style>.profile-grid{display:grid;grid-template-columns:1fr 1.2fr;gap:22px}.profile-card{display:flex;flex-direction:column;align-items:flex-start}.profile-avatar{width:72px;height:72px;border-radius:20px;background:var(--lime);color:var(--green);display:grid;place-items:center;font:700 32px 'Space Grotesk';margin-bottom:18px}.profile-name{display:flex;align-items:center;gap:10px;flex-wrap:wrap}.profile-name h2{font-size:20px}.profile-details{width:100%;display:grid;gap:14px;margin-top:28px;padding-top:20px;border-top:1px solid var(--line)}.profile-details div{display:flex;justify-content:space-between;gap:15px;font-size:13px}.profile-details span{color:var(--muted)}.profile-details strong{text-align:right;overflow-wrap:anywhere}.profile-intro{font-size:13px;line-height:1.6;margin:0 0 22px}.profile-form .field{margin-bottom:15px}.profile-form small{display:block;margin-top:5px;font-size:11px}.profile-form .btn{margin-top:4px}@media(max-width:760px){.profile-grid{grid-template-columns:1fr;gap:14px}.profile-card{padding:18px}.profile-details div{display:block}.profile-details strong{display:block;text-align:left;margin-top:4px}.profile-name h2{font-size:18px}}</style>
