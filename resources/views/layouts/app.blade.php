<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Manajemen RT.011')</title>
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1b3a53; /* Deep Navy */
            --secondary-color: #20B2AA; /* Turquoise */
            --accent-color: #f7a800; /* Amber Warning */
            --bg-light: #f4f6fa;
            --text-dark: #2c3e50;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar-custom {
            background-color: var(--primary-color) !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .navbar-custom .navbar-brand {
            color: #ffffff !important;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .navbar-custom .nav-link {
            color: rgba(255, 255, 255, 0.8) !important;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .navbar-custom .nav-link:hover, 
        .navbar-custom .nav-link.active {
            color: var(--secondary-color) !important;
        }

        .card-custom {
            background: #ffffff;
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.02);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .btn-primary-custom {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: #ffffff;
            font-weight: 600;
            border-radius: 10px;
            padding: 10px 20px;
            transition: all 0.2s ease;
        }

        .btn-primary-custom:hover {
            background-color: #12283a;
            border-color: #12283a;
            color: #ffffff;
            transform: translateY(-1px);
        }

        .btn-secondary-custom {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            color: #ffffff;
            font-weight: 600;
            border-radius: 10px;
            padding: 10px 20px;
            transition: all 0.2s ease;
        }

        .btn-secondary-custom:hover {
            background-color: #1a9690;
            border-color: #1a9690;
            color: #ffffff;
            transform: translateY(-1px);
        }

        .footer {
            margin-top: auto;
            background-color: var(--primary-color);
            color: rgba(255, 255, 255, 0.6);
            padding: 20px 0;
            font-size: 0.9rem;
        }

        /* Status badges */
        .badge-lunas {
            background-color: rgba(32, 178, 170, 0.15) !important;
            color: var(--secondary-color) !important;
            border: 1px solid var(--secondary-color);
        }

        .badge-tunggak {
            background-color: rgba(220, 53, 69, 0.15) !important;
            color: #dc3545 !important;
            border: 1px solid #dc3545;
        }

        /* Form styling */
        .form-control, .form-select {
            border-radius: 10px;
            padding: 10px 15px;
            border: 1px solid #dcdfe6;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 3px rgba(32, 178, 170, 0.2);
        }

        /* Checkbox styling */
        .checkbox-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 10px;
        }

        .checkbox-btn {
            display: block;
            background-color: #fff;
            border: 1px solid #dcdfe6;
            border-radius: 8px;
            padding: 8px;
            text-align: center;
            cursor: pointer;
            user-select: none;
            transition: all 0.2s ease;
        }

        .checkbox-input {
            display: none;
        }

        .checkbox-input:checked + .checkbox-btn {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            color: white;
            font-weight: bold;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background-color: #fff !important;
                color: #000 !important;
            }
            .card {
                box-shadow: none !important;
                border: 1px solid #000 !important;
            }
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Navigation Header -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom no-print">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('public.dashboard') }}">
                <i class="bi bi-shield-check me-2" style="font-size: 1.5rem; color: var(--secondary-color);"></i>
                <span>RT.011 KARANGGINTUNG</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    @auth
                        <li class="nav-item">
                            <a class="nav-link {{ Route::is('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                <i class="bi bi-speedometer2 me-1"></i> Dashboard Admin
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Route::is('residents.index') ? 'active' : '' }}" href="{{ route('residents.index') }}">
                                <i class="bi bi-people me-1"></i> Data Warga
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ Request::is('admin/payments*') ? 'active' : '' }}" href="#" id="paymentsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-cash-stack me-1"></i> Transaksi
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" aria-labelledby="paymentsDropdown" style="border-radius: 12px;">
                                <li><a class="dropdown-item" href="{{ route('payments.create') }}"><i class="bi bi-plus-circle me-2"></i> Input Transaksi</a></li>
                                <li><a class="dropdown-item" href="{{ route('payments.index') }}"><i class="bi bi-list-ul me-2"></i> Riwayat Kas & Keamanan</a></li>
                                <li><a class="dropdown-item" href="{{ route('payments.status') }}"><i class="bi bi-file-earmark-check me-2"></i> Status Pembayaran</a></li>
                                <li><a class="dropdown-item" href="{{ route('payments.riwayat') }}"><i class="bi bi-calendar-event me-2"></i> Rekap Iuran Bulanan</a></li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Route::is('letters.index') ? 'active' : '' }}" href="{{ route('letters.index') }}">
                                <i class="bi bi-envelope me-1"></i> Surat RT
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Route::is('reports.index') ? 'active' : '' }}" href="{{ route('reports.index') }}">
                                <i class="bi bi-file-earmark-spreadsheet me-1"></i> Laporan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Route::is('backup.index') ? 'active' : '' }}" href="{{ route('backup.index') }}">
                                <i class="bi bi-cloud-arrow-up me-1"></i> Backup
                            </a>
                        </li>
                        <li class="nav-item ms-lg-3">
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-light btn-sm px-3" style="border-radius: 8px;">
                                    <i class="bi bi-box-arrow-right me-1"></i> Keluar
                                </button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('public.dashboard') }}"><i class="bi bi-house-door me-1"></i> Beranda</a>
                        </li>
                        <li class="nav-item ms-lg-3">
                            <a href="{{ route('login') }}" class="btn btn-secondary-custom btn-sm px-3" style="border-radius: 8px;">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Login Admin
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content Area -->
    <div class="container my-4 py-2">
        <!-- Toast Alerts / Session Messages -->
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show mb-4 d-flex align-items-center" role="alert" style="border-radius: 12px;">
                <i class="bi bi-check-circle-fill me-2 fs-5 text-success"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error') || $errors->any())
            <div class="alert alert-danger border-0 shadow-sm alert-dismissible fade show mb-4 d-flex align-items-center" role="alert" style="border-radius: 12px;">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5 text-danger"></i>
                <div>
                    @if(session('error'))
                        {{ session('error') }}
                    @else
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <!-- Footer -->
    <footer class="footer no-print text-center">
        <div class="container">
            <span>&copy; {{ date('Y') }} RT.011 RW.003 Karanggintung. Semua Hak Cipta Dilindungi.</span>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
