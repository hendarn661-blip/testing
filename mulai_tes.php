<!DOCTYPE html>
<html lang="id">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<head>
    <title>Mulai Tes Kecermatan</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.8/css/bootstrap.min.css">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="card p-4 shadow text-center" style="max-width: 550px;">
        <h3>Tes Kecermatan Angka Hilang</h3>
        <form action="cermat?action=halaman_tes" method="POST">
            <div class="form-group">
                <label>Nama Anda :</label>        
                <input type="text" name="nama" class="form-control mb-3" placeholder="Nama Lengkap" required>
            </div>
            <div class="form-check form-switch">
              <input name="timer" class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault" checked>
              <label class="form-check-label" for="flexSwitchCheckDefault">Timer On/Off </label>
            </div>

            <button type="submit" class="btn btn-primary w-100">Mulai Ujian</button>

        </form>
    </div>
</body>
</html>
