<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cermat extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->model('TesModel');
    }

    public function index() {
        $this->load->view('mulai_tes');
    }

    public function halaman_tes() {
        // 1. Generate Kunci Statis untuk paket ini (5 angka unik)
        
        $nama = $this->input->post('nama');
        $timer  = $this->input->post('timer') !== null ? 1 : 0;
        
        $this->session->set_userdata('nama_peserta',$nama !== null ? $nama : 'Peserta');
       
        
        $digits = range(0, 9);
        shuffle($digits);
        $kunci = array_slice($digits, 0, 5); 
        
        // Simpan kunci di session agar bisa dicocokkan saat koreksi akhir
        $this->session->set_userdata('kunci_aktif', $kunci);

        // 2. Generate 15 Baris Soal sekaligus berdasarkan kunci di atas
        $daftar_soal = [];
        for ($i = 1; $i <= 15; $i++) {
            // Pilih acak salah satu angka dari kunci untuk dihilangkan
            $indeks_hilang = array_rand($kunci);
            $angka_hilang = $kunci[$indeks_hilang];

            // Buat baris soal (4 angka sisa)
            $soal_4_angka = $kunci;
            unset($soal_4_angka[$indeks_hilang]);
            $soal_4_angka = array_values($soal_4_angka);
            shuffle($soal_4_angka); // Acak urutan tampilnya angka di baris soal

            // Simpan data soal dan kunci jawaban benarnya (A, B, C, D, atau E)
            $huruf_jawaban = chr(65 + $indeks_hilang);
            
            $daftar_soal[$i] = [
                'angka_soal' => $soal_4_angka,
                'jawaban_benar' => $huruf_jawaban
            ];
        }

        // Simpan daftar soal ke session untuk validasi pasca-submit
        $this->session->set_userdata('daftar_soal_aktif', $daftar_soal);

        // Kirim data kunci dan soal ke View
        $data['kunci'] = $kunci;
        $data['daftar_soal'] = $daftar_soal;
        $data['timer'] = $timer;
        $this->load->view('halaman_tes', $data);
    }

    public function submit_kolektif() {
        $jawaban_user = $this->input->post('jawaban'); // Array berisi jawaban per nomor
        $daftar_soal = $this->session->userdata('daftar_soal_aktif');
        
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

        // Siapkan data untuk view selesai atau simpan ke DB
        $data_selesai = [
            'nama_peserta' => $this->session->userdata('nama_peserta') !==null  ? $this->session->userdata('nama_peserta') : 'Unknown',
            'skor'         => $skor_benar,
            'total_soal'   => count($daftar_soal)
        ];

         if (!empty($data_selesai['nama_peserta'])) {
            $this->TesModel->simpan_skor($data_selesai);
        }
        $this->load->view('selesai_tes', $data_selesai);
    }

    public function ambil_soal() {
        // 1. Ambil 5 angka acak unik untuk kunci (0-9)
        $digits = range(0, 9);
        shuffle($digits);
        $kunci = array_slice($digits, 0, 5); // Contoh: [3, 7, 1, 9, 5]

        // 2. Tentukan 1 angka yang hilang dari kunci
        $indeks_hilang = array_rand($kunci);
        $angka_hilang = $kunci[$indeks_hilang];

        // 3. Buat kombinasi 4 angka untuk soal (kunci dikurangi angka hilang)
        $soal = $kunci;
        unset($soal[$indeks_hilang]);
        $soal = array_values($soal);
        shuffle($soal); // Acak urutan tampilan soal

        // 4. Konversi pilihan jawaban huruf A, B, C, D, E berdasarkan posisi di baris kunci
        $huruf_jawaban = chr(65 + $indeks_hilang); // A, B, C, D, atau E

        // Simpan jawaban benar di session untuk divalidasi saat submit
        $this->session->set_userdata('jawaban_benar', $huruf_jawaban);

        echo json_encode([
            'kunci' => $kunci,
            'soal' => $soal
        ]);
    }

    public function kirim_jawaban() {
        $jawaban_user = strtoupper($this->input->post('jawaban'));
        $jawaban_benar = $this->session->userdata('jawaban_benar');

        $skor = $this->session->userdata('skor');
        $total_soal = $this->session->userdata('total_soal') + 1;
        $this->session->set_userdata('total_soal', $total_soal);

        $status = false;
        if ($jawaban_user === $jawaban_benar) {
            $skor++;
            $this->session->set_userdata('skor', $skor);
            $status = true;
        }

        echo json_encode([
            'status' => $status,
            'skor' => $skor,
            'total_soal' => $total_soal
        ]);
    }

    public function selesai() {
        $data_simpan = [
            'nama_peserta' => $this->session->userdata('nama_peserta'),
            'skor'         => $this->session->userdata('skor'),
            'total_soal'   => $this->session->userdata('total_soal')
        ];

        if (!empty($data_simpan['nama_peserta'])) {
            $this->TesModel->simpan_skor($data_simpan);
        }

        $this->load->view('selesai_tes', $data_simpan);
    }
}
