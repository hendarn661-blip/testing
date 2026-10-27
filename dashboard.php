<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Panel Admin - Tes Kecermatan</title>
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.8/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.13.1/font/bootstrap-icons.css">
    <!-- <link rel="stylesheet" href="'assets/plugins/font-awesome-4.4.0/css/font-awesome.min.css')?>"> -->
</head>
<body class="bg-light">

    <!-- Navigasi Atas -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">
                <i class="bi bi-shield-check text-warning me-2"></i>Admin Panel Tes Kecermatan
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link text-white" href="cermat" target="_blank">
                    <i class="bi bi-box-arrow-up-right me-1"></i> Buka Halaman Tes
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Judul Halaman -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4 mb-0 fw-bold text-secondary">Dashboard Ringkasan Nilai</h2>
            <button class="btn btn-outline-primary btn-sm" onclick="window.location.reload();">
                <i class="bi bi-arrow-clockwise"></i> Refresh Data
            </button>
        </div>

        <!-- Baris Ringkasan Ringkas (KPI Cards) -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <div class="card shadow-sm border-0 bg-white p-3 h-100">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted text-uppercase mb-1" style="font-size: 0.8rem; font-weight: 700;">Total Peserta</h6>
                            <h3 class="mb-0 fw-bold"><?= $total_peserta; ?> <span class="fs-6 fw-normal text-muted">orang</span></h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded-3 text-primary">
                            <i class="bi bi-people fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-12 col-md-4">
                <div class="card shadow-sm border-0 bg-white p-3 h-100">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted text-uppercase mb-1" style="font-size: 0.8rem; font-weight: 700;">Nilai Rata-Rata</h6>
                            <h3 class="mb-0 fw-bold text-success"><?= $rata_rata_skor; ?></h3>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded-3 text-success">
                            <i class="bi bi-graph-up-arrow fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="card shadow-sm border-0 bg-white p-3 h-100">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted text-uppercase mb-1" style="font-size: 0.8rem; font-weight: 700;">Skor Tertinggi</h6>
                            <h3 class="mb-0 fw-bold text-danger"><?= $skor_tertinggi; ?></h3>
                        </div>
                        <div class="bg-danger bg-opacity-10 p-3 rounded-3 text-danger">
                            <i class="bi bi-trophy fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Riwayat Nilai Peserta -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0 fw-bold text-dark"><i class="bi bi-table me-2 text-primary"></i>Riwayat Hasil Ujian</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-center">
                        <thead class="table-light text-secondary small text-uppercase">
                            <tr>
                                <th style="width: 8%;">No</th>
                                <th class="text-start">Nama Lengkap Peserta</th>
                                <th>Skor Benar</th>
                                <th>Total Soal</th>
                                <th>Akurasi (%)</th>
                                <th>Tanggal Tes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($riwayat_ujian)): ?>
                            <tr>
                                <td colspan="6" class="text-muted py-4">Belum ada data peserta yang mengikuti tes.</td>
                            </tr>
                            <?php else: ?>
                                <?php $no = 1; foreach($riwayat_ujian as $row): 
                                    // Hitung persentase akurasi jawaban benar
                                    $akurasi = ($row['total_soal'] > 0) ? round(($row['skor'] / $row['total_soal']) * 100, 1) : 0;
                                ?>
                                <tr>
                                    <td><strong><?= $no++; ?></strong></td>
                                    <td class="text-start fw-semibold text-dark"><?= htmlspecialchars($row['nama_peserta']); ?></td>
                                    <td><span class="badge bg-success bg-opacity-10 text-success px-3 py-2 fs-6 rounded-pill"><?= $row['skor']; ?></span></td>
                                    <td class="text-muted"><?= $row['total_soal']; ?></td>
                                    <td>
                                        <div class="fw-bold <?= $akurasi >= 70 ? 'text-success' : ($akurasi >= 50 ? 'text-warning' : 'text-danger'); ?>">
                                            <?= $akurasi; ?>%
                                        </div>
                                    </td>
                                    <td class="text-muted small"><?= date('d M Y, H:i', strtotime($row['tanggal'])); ?> WIB</td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-center text-muted small mt-5 mb-4">
        &copy; <?= date('Y'); ?> Panel Admin Kecermatan Angka Hilang. All rights reserved.
    </footer>

</body>
</html>
