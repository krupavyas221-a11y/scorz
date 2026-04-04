@extends('superadmin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Dashboard</h1>
        <p>Welcome back, {{ Auth::guard('superadmin')->user()->name }}.</p>
    </div>
</div>

<div class="grid-3">
    <div class="card">
        <div class="card-body">
            <div style="color:var(--muted);font-size:.78rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em">Schools</div>
            <div style="font-size:2rem;font-weight:800;color:#f1f5f9;margin:.3rem 0">{{ \App\Models\School::count() }}</div>
            <div style="font-size:.78rem;color:var(--muted)">Total registered</div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div style="color:var(--muted);font-size:.78rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em">Users</div>
            <div style="font-size:2rem;font-weight:800;color:#f1f5f9;margin:.3rem 0">{{ \App\Models\User::count() }}</div>
            <div style="font-size:.78rem;color:var(--muted)">Admins &amp; teachers</div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div style="color:var(--muted);font-size:.78rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em">Pupils</div>
            <div style="font-size:2rem;font-weight:800;color:#f1f5f9;margin:.3rem 0">{{ \App\Models\Pupil::count() }}</div>
            <div style="font-size:.78rem;color:var(--muted)">Across all schools</div>
        </div>
    </div>
</div>
@endsection
