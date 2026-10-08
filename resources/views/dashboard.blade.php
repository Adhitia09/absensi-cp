@extends('layouts.app')

@section('content')
<div class="content">
    <div class="headline">
        <div><div class="eyebrow">Ringkasan operasional</div><h1>Halo, {{ explode(' ', auth()->user()->name)[0] }} 👋</h1></div>
        <div class="date">{{ now()->translatedFormat('l, d F Y') }}</div>
    </div>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-error">{{ session('error') }}</div>@endif
    @if(session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
    @endif
    @if($errors->any())<div class="alert alert-error">{{ $errors->first() }}</div>@endif

    @if($isAdmin && $pendingLeaveCount > 0)
        <div class="alert alert-info">Ada <strong>{{ $pendingLeaveCount }}</strong> pengajuan izin yang menunggu persetujuan. <a href="{{ route('leaves.index') }}" style="color:inherit;font-weight:700">Tinjau sekarang →</a></div>
    @endif

    <div class="cards">
        <div class="stat"><div class="stat-top"><span>Pegawai aktif</span><div class="stat-icon">♙</div></div><strong>{{ $employeeCount }}</strong><small class="muted">orang terdaftar</small></div>
        <div class="stat"><div class="stat-top"><span>Kehadiran hari ini</span><div class="stat-icon">✓</div></div><strong>{{ $presentCount }}</strong><small class="muted">dari {{ $employeeCount }} pegawai</small></div>
        <div class="stat"><div class="stat-top"><span>Total catatan</span><div class="stat-icon">▤</div></div><strong>{{ $todayCount }}</strong><small class="muted">absensi hari ini</small></div>
    </div>

    <div class="grid">
        <section class="panel action-panel">
            @if($isAdmin)
                <div class="panel-head"><h2>Panel admin</h2><span style="font-size:12px;color:#b7d63d">● Aktif</span></div>
                <p>Kelola data pegawai dan pantau riwayat kehadiran rumah makan.</p>
                <a class="btn btn-primary" style="display:inline-block;text-decoration:none;margin-top:18px" href="{{ route('employees.index') }}">Kelola pegawai</a>
                <a class="btn btn-light" style="display:inline-block;text-decoration:none;margin:18px 0 0 6px" href="{{ route('attendance.history') }}">Lihat riwayat</a>
            @else
                <div class="panel-head"><h2>Absensi saya</h2><span style="font-size:12px;color:#b7d63d">● Live</span></div>
                <p>Catat waktu kehadiranmu saat mulai dan selesai bekerja.</p>
                <div class="clock" id="clock">--:--:--</div>
                <div class="action-buttons">
                    <form class="gps-form" method="POST" action="{{ route('attendance.check-in') }}">@csrf<input type="hidden" name="latitude"><input type="hidden" name="longitude"><button class="btn btn-primary" @disabled($attendance?->check_in)>Absen masuk</button></form>
                    <form class="gps-form" method="POST" action="{{ route('attendance.check-out') }}">@csrf<input type="hidden" name="latitude"><input type="hidden" name="longitude"><button class="btn btn-light" @disabled(!$attendance?->check_in || $attendance?->check_out)>Absen pulang</button></form>
                </div>
                @if($attendance)
                    <p style="margin:18px 0 0;font-size:12px">Tanggal: <strong>{{ $attendance->date?->translatedFormat('l, d F Y') }}</strong><br>Masuk: <strong class="{{ $attendance->status === 'Terlambat' ? 'time-late' : '' }}">{{ $attendance->check_in?->format('H:i') ?? '—' }}</strong> &nbsp; Pulang: <strong>{{ $attendance->check_out?->format('H:i') ?? '—' }}</strong></p><div class="gps-list">@if($attendance->check_in_latitude !== null)<a class="gps-location" href="https://www.google.com/maps/search/?api=1&query={{ $attendance->check_in_latitude }},{{ $attendance->check_in_longitude }}" target="_blank" rel="noopener"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21s7-6.1 7-12A7 7 0 0 0 5 9c0 5.9 7 12 7 12Z"/><circle cx="12" cy="9" r="2.2"/></svg><span>GPS masuk <small>{{ $attendance->check_in_latitude }}, {{ $attendance->check_in_longitude }}</small></span></a>@endif @if($attendance->check_out_latitude !== null)<a class="gps-location" href="https://www.google.com/maps/search/?api=1&query={{ $attendance->check_out_latitude }},{{ $attendance->check_out_longitude }}" target="_blank" rel="noopener"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21s7-6.1 7-12A7 7 0 0 0 5 9c0 5.9 7 12 7 12Z"/><circle cx="12" cy="9" r="2.2"/></svg><span>GPS pulang <small>{{ $attendance->check_out_latitude }}, {{ $attendance->check_out_longitude }}</small></span></a>@endif</div>
                @endif
                <form method="GET" action="{{ route('attendance.export') }}" style="margin-top:24px;display:flex;gap:8px;align-items:center"><input type="month" name="month" value="{{ now()->format('Y-m') }}" class="filter-month"><button class="btn btn-light">Export absensi</button></form>
            @endif
        </section>

        <section class="panel">
            <div class="panel-head"><h2>Aktivitas terbaru</h2><span class="muted" style="font-size:12px">8 catatan terakhir</span></div>
            <div class="table-wrap"><table><thead><tr><th>Nama pegawai</th><th>Tanggal</th><th>Jam masuk</th><th>Jam pulang</th><th>Status</th></tr></thead><tbody>
            @forelse($recentAttendances as $item)
                <tr><td>{{ $item->user->name }}</td><td>{{ $item->date?->format('d/m/Y') }}</td><td class="{{ $item->status === 'Terlambat' ? 'time-late' : '' }}">{{ $item->check_in?->format('H:i') ?? '—' }}</td><td>{{ $item->check_out?->format('H:i') ?? '—' }}</td><td><span class="pill {{ $item->status === 'Terlambat' ? 'late' : '' }}">{{ $item->status }}</span></td></tr>
            @empty
                <tr><td colspan="5" class="muted">Belum ada catatan absensi.</td></tr>
            @endforelse
            </tbody></table></div>
        </section>
    </div>
</div>

@if(!$isAdmin)
<script>function tick(){const clock=document.getElementById('clock');if(clock)clock.textContent=new Date().toLocaleTimeString('id-ID',{hour12:false})}tick();setInterval(tick,1000);document.querySelectorAll('.gps-form').forEach(form=>form.addEventListener('submit',function(event){event.preventDefault();const button=form.querySelector('button');button.disabled=true;button.textContent='Membaca lokasi...';if(!navigator.geolocation){alert('Browser ini tidak mendukung GPS.');button.disabled=false;return;}navigator.geolocation.getCurrentPosition(position=>{form.querySelector('[name=\"latitude\"]').value=position.coords.latitude;form.querySelector('[name=\"longitude\"]').value=position.coords.longitude;form.submit();},()=>{alert('Lokasi tidak bisa dibaca. Izinkan akses lokasi browser lalu coba lagi.');button.disabled=false;button.textContent=form.action.includes('check-in')?'Absen masuk':'Absen pulang';},{enableHighAccuracy:true,timeout:10000,maximumAge:0});}));</script>
@endif
@endsection
