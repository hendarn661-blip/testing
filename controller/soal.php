<?php
// Aktifkan session di bagian paling atas script
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('koneksi.php');

// Simulasi pengganti Model DB (Sesuaikan dengan koneksi database Anda jika ada)
function simpan_skor_db($data) {
    global $conn;   
    $sql = "INSERT INTO riwayat_tes (nama_peserta, skor, total_soal) VALUES (?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) 
    {
            // "sii" berarti: s = string (nama), i = integer (skor), i = integer (total_soal)
            $stmt->bind_param("sii", $data['nama_peserta'], $data['skor'], $data['total_soal']);
            
            $stmt->execute();
            return true; 
    }
    return  false;
}

// Menentukan fungsi mana yang dijalankan berdasarkan parameter URL ?action=
$action = isset($_POST['action']) ? $_POST['action'] : 'index';

switch ($action) {
    case 'index':
        index();
        break;
        
    case 'halaman_tes':
        halaman_tes();
        break;
        
    case 'submit_kolektif':
        submit_kolektif();
        break;
        
    case 'ambil_soal':
        ambil_soal();
        break;
    case 'selesai':
        selesai();
        break;
        
    default:
        index();
        break;
}

// ==========================================
// FUNGSI - FUNGSI CONTROLLER
// ==========================================

function index() {
    // Memuat file view (pastikan file 'mulai_tes.php' ada di folder yang sama)
    if (file_exists('../mulai_tes.php')) {
        include '../mulai_tes.php';
    } else {
       echo "View mulai_tes.php tidak ditemukan.";
    }
}

function halaman_tes() {
    global $conn;

    $nama = isset($_POST['nama']) && trim($_POST['nama']) !== '' ? $_POST['nama'] : 'Peserta';
    $timer = isset($_POST['timer']) ? 1 : 0;
   
    // 1. Generate Kunci Statis (5 angka unik)
    $digits = range(1, 9);
    shuffle($digits);
    $kunci = array_slice($digits, 0, 5); 

    // 2. Generate 45 Baris Soal sekaligus
    $daftar_soal = [];
    for ($i = 1; $i <= 45; $i++) {
        $indeks_hilang = array_rand($kunci);
        $angka_hilang = $kunci[$indeks_hilang];

        $soal_4_angka = $kunci;
        unset($soal_4_angka[$indeks_hilang]);
        $soal_4_angka = array_values($soal_4_angka);
        shuffle($soal_4_angka); 

        $huruf_jawaban = chr(65 + $indeks_hilang);
        
        $daftar_soal[$i] = [
            'angka_soal' => $soal_4_angka,
            'jawaban_benar' => $huruf_jawaban
        ];
    }

    // 3. Simpan ke Database menggunakan MySQLi
    $kunci_json = json_encode($kunci);
    $soal_json  = json_encode($daftar_soal);

    $sql = "INSERT INTO sesi_tes (nama_peserta, kunci_aktif, daftar_soal_aktif, timer) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    
    // "sssi" = String (nama), String (kunci_json), String (soal_json), Integer (timer)
    mysqli_stmt_bind_param($stmt, "sssi", $nama, $kunci_json, $soal_json, $timer);
    mysqli_stmt_execute($stmt);

    // Ambil ID sesi tes yang baru saja disimpan
    $id_tes = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    // 4. Kirim data ke View (Sertakan $id_tes)
    $data = [
        'id_tes'      => $id_tes,
        'kunci'       => $kunci,
        'daftar_soal' => $daftar_soal,
        'timer'       => $timer
    ];
    
    extract($data);
    if (file_exists('../halaman_tes.php')) {
        include '../halaman_tes.php';
    } else {
        echo "View halaman_tes.php tidak ditemukan.";
    }
}

function submit_kolektif() {
    global $conn;

    $id_tes = isset($_POST['id_tes']) ? (int)$_POST['id_tes'] : 0;
    $jawaban_user = isset($_POST['jawaban']) ? $_POST['jawaban'] : []; 

    // 1. Ambil data soal & nama peserta dari database berdasarkan id_tes
    $sql = "SELECT nama_peserta, daftar_soal_aktif FROM sesi_tes WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id_tes);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    $sesi = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if (!$sesi) {
        die("Data sesi tes tidak ditemukan.");
    }

    // 2. Hitung skor jawaban
    $daftar_soal = json_decode($sesi['daftar_soal_aktif'], true);
    $skor_benar = 0;

    if (!empty($jawaban_user)) {
        foreach ($jawaban_user as $nomor => $jawaban) {
            if (isset($daftar_soal[$nomor])) {
                if (strtoupper($jawaban) === $daftar_soal[$nomor]['jawaban_benar']) {
                    $skor_benar++;
                }
            }
        }
    }

    $data_selesai = [
        'nama_peserta' => $sesi['nama_peserta'],
        'skor'         => $skor_benar,
        'total_soal'   => count($daftar_soal)
    ];

    if (!empty($data_selesai['nama_peserta'])) {
        simpan_skor_db($data_selesai);
    }
    
    extract($data_selesai);
    if (file_exists('../selesai_tes.php')) {
        include '../selesai_tes.php';
    } else {
        echo "View selesai_tes.php tidak ditemukan.";
    }
}

/*function simpan_skor_db($data) {
    global $conn;

    $sql = "INSERT INTO hasil_tes (nama_peserta, skor, total_soal) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);

    // "sii" = String (nama_peserta), Integer (skor), Integer (total_soal)
    mysqli_stmt_bind_param($stmt, "sii", $data['nama_peserta'], $data['skor'], $data['total_soal']);
    
    $berhasil = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return $berhasil;
}
*/

function selesai() {
    $data_simpan = [
        'nama_peserta' => isset($_SESSION['nama_peserta']) ? $_SESSION['nama_peserta'] : 'Unknown',
        'skor'         => isset($_SESSION['skor']) ? $_SESSION['skor'] : 0,
        'total_soal'   => isset($_SESSION['total_soal']) ? $_SESSION['total_soal'] : 0
    ];

    if (!empty($data_simpan['nama_peserta'])) {
        simpan_skor_db($data_simpan);
    }

    extract($data_simpan);
    if (file_exists('../selesai_tes.php')) {
        include '../selesai_tes.php';
    } else {
        echo "View selesai_tes.php tidak ditemukan.";
    }
}



