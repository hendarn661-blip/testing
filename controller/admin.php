<?php
include('koneksi.php');
function ambil_semua_riwayat() {
    global $conn;
    
    $sql = "SELECT * FROM riwayat_tes ORDER BY tanggal DESC";
    $result = mysqli_query($conn, $sql);
    
    $riwayat = [];
    if ($result) {
        // Ambil semua data sebagai array asosiatif (setara result_array() di CI3)
        while ($row = mysqli_fetch_assoc($result)) {
            $riwayat[] = $row;
        }
    }
    return $riwayat;
}

function hitung_total_peserta() {
    global $conn;
    
    $sql = "SELECT COUNT(*) as total FROM riwayat_tes";
    $result = mysqli_query($conn, $sql);
    
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return (int) $row['total'];
    }
    return 0;
}

function hitung_rata_rata_skor() {
    global $conn;
    
    $sql = "SELECT AVG(skor) as skor FROM riwayat_tes";
    $result = mysqli_query($conn, $sql);
    
    $data = ['skor' => 0];
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        // Jika data kosong/null, set default ke 0
        $data['skor'] = $row['skor'] !== null ? $row['skor'] : 0;
    }
    return $data; // Mengembalikan array dengan key 'skor' sesuai kebutuhan controller
}

function ambil_skor_tertinggi() {
    global $conn;
    
    $sql = "SELECT MAX(skor) as skor FROM riwayat_tes";
    $result = mysqli_query($conn, $sql);
    
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return $row['skor'] !== null ? (int) $row['skor'] : 0;
    }
    return 0;
}


// Aktifkan session jika nanti dashboard admin membutuhkan validasi login/session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}



// =========================================================================
// ROUTING BERSAHAJA (Mengatur fungsi yang dipanggil lewat URL)
// =========================================================================
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

switch ($action) {
    case 'index':
        index();
        break;
        
    default:
        index();
        break;
}


// =========================================================================
// FUNGSI UTAMA (CONTROLLER)
// =========================================================================

function index() {
    // 1. Mengambil data ringkasan statis (Ringkasan KPI) dari fungsi model di atas
    $avg_data = hitung_rata_rata_skor();
    
    $data['total_peserta']  = hitung_total_peserta();
    $data['skor_tertinggi'] = ambil_skor_tertinggi();
    $data['rata_rata_skor'] = isset($avg_data['skor']) ? round($avg_data['skor'], 1) : 0;
    
    // 2. Mengambil daftar seluruh riwayat ujian peserta
    $data['riwayat_ujian']  = ambil_semua_riwayat();

    // 3. Melempar data dan memuat file tampilan dashboard admin
    extract($data);
    
    // Sesuaikan path folder view admin/dashboard.php Anda
    if (file_exists('../dashboard.php')) {
        include '../dashboard.php';
    } else {
        echo "View admin/dashboard.php tidak ditemukan. Pastikan folder 'admin' dan file 'dashboard.php' berada di direktori yang benar.";
    }
}
