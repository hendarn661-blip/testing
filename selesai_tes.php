<!DOCTYPE html>
<html lang="id">
<head>
    <title>Tes Selesai</title>
    <!-- <link rel="stylesheet" href="https://jsdelivr.net"> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.8/css/bootstrap.min.css">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="height: 100vh;">
    <div class="card p-4 shadow text-center" style="width: 450px;">
        <h3 class="text-success">Waktu Habis!</h3>
        <p class="text-muted">Hasil evaluasi tes kecermatan Anda telah disimpan ke database.</p>
        <hr>
        <table class="table table-bordered my-3">
            <tr><th>Nama</th><td><?= $nama_peserta; ?></td></tr>
            <tr><th>Benar</th><td><span class="badge bg-success fs-6"><?= $skor; ?></span></td></tr>
            <tr><th>Total Soal Kerja</th><td><?= $total_soal; ?></td></tr>
        </table>
        <a href="cermat" class="btn btn-primary w-100">Coba Lagi</a>
    </div>
</body>
</html>
