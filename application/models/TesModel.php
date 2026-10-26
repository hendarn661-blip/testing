<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TesModel extends CI_Model {

    public function simpan_skor($data) {
        return $this->db->insert('riwayat_tes', $data);
    }

    // --- FUNGSI TAMBAHAN UNTUK DASHBOARD ADMIN ---

    public function ambil_semua_riwayat() {
        $this->db->order_by('tanggal', 'DESC');
        return $this->db->get('riwayat_tes')->result_array();
    }

    public function hitung_total_peserta() {
        return $this->db->count_all('riwayat_tes');
    }

    public function hitung_rata_rata_skor() {
        $this->db->select_avg('skor');
        $query = $this->db->get('riwayat_tes')->row();
        return json_decode(json_encode($query), true); // Pastikan berbentuk array/object standar
    }

    public function ambil_skor_tertinggi() {
        $this->db->select_max('skor');
        $query = $this->db->get('riwayat_tes')->row_array();
        return $query['skor'] ;
    }
}
