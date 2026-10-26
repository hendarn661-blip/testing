<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Registrasi_model extends CI_Model {

    var $table = 'tb_registrasi';
    var $table_join = 'tb_client';
    var $id_join_a = 'id_client';
    var $id_join_b = 'id_client';
    var $column_order = array(null, 'no_registrasi', 'nama_client', 'status');
    var $column_search = array('no_registrasi', 'nama_client', 'status');

    var $column_select = 'id_registrasi,no_registrasi, nama_client, status';
    var $order = array('no_registrasi' => 'desc');

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    private function _get_datatables_query() {
        $this->db->from($this->table);
        $this->db->select( $this->column_select);
        $this->db->join($this->table_join,"$this->table.$this->id_join_a=$this->table_join.$this->id_join_b","inner");
        $i = 0;
        foreach ($this->column_search as $item) {
            if($_POST['search']['value']) {
                if($i===0) {
                    $this->db->group_start();
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }
                if(count($this->column_search) - 1 == $i)
                    $this->db->group_end();
            }
            $i++;
        }
        
        if(isset($_POST['order'])) {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if(isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function get_datatables() {
        $this->_get_datatables_query();
        if($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    function get($id, $select=null)
    {
        if($select) $this->db->select($select);
        $this->db->where(array("id_registrasi"=>$id));
        $this->db->get($this->table)->result();
        
    }

    function count_filtered() {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all() {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

    function insert($data)
    {
        return $this->db->insert($this->table,$data) OR die("Please use set_table function for set Table Name");
    }
    function get_id_insert()
    {
        return $this->db->insert_id();
    }
    function update($data, $id)
    {
        $this->db->where(array("id_registrasi"=>$id));
        return $this->db->update($this->table, $data);

    }
}
