<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview Surat RT.011</title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6fa;
            font-family: 'Times New Roman', Times, serif;
            padding: 20px;
        }

        .preview-container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            max-width: 800px;
            margin: 0 auto;
            padding: 60px 80px;
        }

        .letter-content {
            white-space: pre-wrap;
            font-size: 12pt;
            line-height: 1.6;
            color: #000000;
        }

        .control-panel {
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        @media print {
            body {
                background-color: #ffffff !important;
                padding: 0 !important;
            }
            .control-panel {
                display: none !important;
            }
            .preview-container {
                box-shadow: none !important;
                padding: 20px 40px !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
            }
        }
    </style>
</head>
<body>

    <!-- CONTROL PANEL FOR ACTIONS -->
    <div class="control-panel d-flex justify-content-between align-items-center no-print">
        <span class="fw-bold text-muted small"><i class="bi bi-file-earmark-pdf me-1"></i>PREVIEW SURAT RT.011</span>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-primary btn-sm px-3">
                <i class="bi bi-printer me-1"></i> Cetak Surat
            </button>
            <form action="{{ route('letters.export-word') }}" method="POST" class="d-inline">
                @csrf
                <input type="hidden" name="text" value="{{ $isi }}">
                <button type="submit" class="btn btn-outline-success btn-sm px-3">
                    <i class="bi bi-file-word me-1"></i> Export ke Word (.doc)
                </button>
            </form>
            <button onclick="window.close()" class="btn btn-outline-secondary btn-sm px-3">Tutup</button>
        </div>
    </div>

    <!-- MAIN LETTER PREVIEW CONTAINER -->
    <div class="preview-container">
        <!-- Letter header kop -->
        <div class="text-center mb-4 pb-2 border-bottom border-dark border-3">
            <h4 class="fw-bold mb-0">RUKUN TETANGGA 011 / RUKUN WARGA 003</h4>
            <h5 class="fw-bold mb-1">PERUMAHAN KARANGGINTUNG</h5>
            <p class="mb-0 small text-muted">Kecamatan Banyumas, Kabupaten Banyumas, Jawa Tengah</p>
        </div>

        <!-- Generated letter body -->
        <div class="letter-content">{{ $isi }}</div>
    </div>

    <!-- Bootstrap 5 JS Bundle CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
