<?php
// Aktifkan session di bagian paling atas script
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Simulasi pengganti Model DB (Sesuaikan dengan koneksi database Anda jika ada)
function simpan_skor_db($data) {
    include('koneksi.php');
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
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

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
        
    case 'kirim_jawaban':
        kirim_jawaban();
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
    $nama = isset($_POST['nama']) ? $_POST['nama'] : null;
    $timer = isset($_POST['timer']) ? 1 : 0;
    
    $_SESSION['nama_peserta'] = ($nama !== null) ? $nama : 'Peserta';
   
    // 1. Generate Kunci Statis (5 angka unik)
    $digits = range(1, 9);
    shuffle($digits);
    $kunci = array_slice($digits, 0, 5); 
    
    $_SESSION['kunci_aktif'] = $kunci;

    // 2. Generate 15 Baris Soal sekaligus
    $daftar_soal = [];
    for ($i = 1; $i <= 15; $i++) {
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

    $_SESSION['daftar_soal_aktif'] = $daftar_soal;

    // Kirim data ke View (Menggunakan extract agar variabel bisa langsung dipakai di view)
    $data = [
        'kunci' => $kunci,
        'daftar_soal' => $daftar_soal,
        'timer' => $timer
    ];
    
    extract($data);
    if (file_exists('../halaman_tes.php')) {
        include '../halaman_tes.php';
    } else {
        echo "View halaman_tes.php tidak ditemukan.";
    }
}

function submit_kolektif() {
    $jawaban_user = isset($_POST['jawaban']) ? $_POST['jawaban'] : []; 
    $daftar_soal = isset($_SESSION['daftar_soal_aktif']) ? $_SESSION['daftar_soal_aktif'] : [];
    
    $skor_benar = 0;
    $total_dijawab = 0;

    if (!empty($jawaban_user)) {
        foreach ($jawaban_user as $nomor => $jawaban) {
            if (isset($daftar_soal[$nomor])) {
                $total_dijawab++;
                if (strtoupper($jawaban) === $daftar_soal[$nomor]['jawaban_benar']) {
                    $skor_benar++;
                }
            }
        }
    }

    $data_selesai = [
        'nama_peserta' => isset($_SESSION['nama_peserta']) ? $_SESSION['nama_peserta'] : 'Unknown',
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

function ambil_soal() {
    header('Content-Type: application/json');

    $digits = range(0, 9);
    shuffle($digits);
    $kunci = array_slice($digits, 0, 5); 

    $indeks_hilang = array_rand($kunci);
    $angka_hilang = $kunci[$indeks_hilang];

    $soal = $kunci;
    unset($soal[$indeks_hilang]);
    $soal = array_values($soal);
    shuffle($soal); 

    $huruf_jawaban = chr(65 + $indeks_hilang); 

    $_SESSION['jawaban_benar'] = $huruf_jawaban;

    echo json_encode([
        'kunci' => $kunci,
        'soal' => $soal
    ]);
    exit;
}

function kirim_jawaban() {
    header('Content-Type: application/json');

    $jawaban_user = isset($_POST['jawaban']) ? strtoupper($_POST['jawaban']) : '';
    $jawaban_benar = isset($_SESSION['jawaban_benar']) ? $_SESSION['jawaban_benar'] : '';

    $skor = isset($_SESSION['skor']) ? $_SESSION['skor'] : 0;
    $total_soal = (isset($_SESSION['total_soal']) ? $_SESSION['total_soal'] : 0) + 1;
    $_SESSION['total_soal'] = $total_soal;

    $status = false;
    if ($jawaban_user === $jawaban_benar) {
        $skor++;
        $_SESSION['skor'] = $skor;
        $status = true;
    }

    echo json_encode([
        'status' => $status,
        'skor' => $skor,
        'total_soal' => $total_soal
    ]);
    exit;
}

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
