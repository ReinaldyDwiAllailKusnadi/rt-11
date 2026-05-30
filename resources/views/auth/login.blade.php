@extends('layouts.app')

@section('title', 'Login Admin - RT.011')

@section('content')
<div class="row justify-content-center align-items-center py-5">
    <div class="col-md-5 col-lg-4">
        <div class="card card-custom border-0 shadow">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="d-inline-flex bg-light p-3 rounded-circle mb-3" style="color: var(--secondary-color);">
                        <i class="bi bi-shield-lock-fill fs-1"></i>
                    </div>
                    <h3 class="fw-bold mb-1" style="color: var(--primary-color);">Login Admin</h3>
                    <p class="text-muted small">Akses Dashboard Manajemen RT.011</p>
                </div>

                <form method="POST" action="{{ route('login.post') }}">
                    @csrf
                    <!-- Username -->
                    <div class="mb-3">
                        <label for="username" class="form-label small fw-bold">Username</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-muted"></i></span>
                            <input type="text" name="username" id="username" class="form-control border-start-0" placeholder="Masukkan username" value="{{ old('username') }}" required autofocus autocomplete="username">
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <label for="password" class="form-label small fw-bold">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                            <input type="password" name="password" id="password" class="form-control border-start-0" placeholder="Masukkan password" required autocomplete="current-password">
                        </div>
                    </div>

                    <!-- Remember me -->
                    <div class="mb-4 d-flex justify-content-between align-items-center">
                        <div class="form-check">
                            <input type="checkbox" name="remember" id="remember" class="form-check-input">
                            <label class="form-check-label small text-muted" for="remember">Ingat Saya</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary-custom w-100 py-2.5">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                    </button>
                </form>

                <div class="text-center mt-4">
                    <a href="{{ route('public.dashboard') }}" class="text-muted small text-decoration-none">
                        <i class="bi bi-arrow-left me-1"></i>Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
