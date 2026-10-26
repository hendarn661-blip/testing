<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('TesModel');
        $this->load->helper('url');
    }

    public function index() {
        // Mengambil data ringkasan statis (Ringkasan KPI)
        $avg_data = $this->TesModel->hitung_rata_rata_skor();
        
        $data['total_peserta']   = $this->TesModel->hitung_total_peserta();
        $data['skor_tertinggi']  = $this->TesModel->ambil_skor_tertinggi();
        $data['rata_rata_skor']  = isset($avg_data['skor']) ? round($avg_data['skor'], 1) : 0;
        
        // Mengambil daftar seluruh riwayat ujian peserta
        $data['riwayat_ujian']   = $this->TesModel->ambil_semua_riwayat();

        $this->load->view('admin/dashboard', $data);
    }
}
