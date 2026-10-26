<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class SHLib_model extends CI_Model
{
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    function view_data($table)
    {      
      return $this->db->get($table);
    }
    function insert_data($table, $data)
    {
        return $this->db->insert($table,$data);
    }

    function get_data($table,$id=null)
    {
        if($id)
        {
            return $this->db->get_where($table,["id"=>$id])->row_array();
        }
        return $this->db->get($table)->result_array();
    }
    function edit_data($table, $id,$data)
    {
        $this->db->where("id",$id);
        return $this->db->update($table,$data);
    }
    function delete_data($table,$id)
    {
        return $this->db->delete($table,["id"=>$id]);
    }
    function total_data($table)
    {       
        $total = $this->db->count_all_results($table);
        return $total;
    }
    function like($field,$search)
    {
        $this->db->like($field, $search);
    }

    function or_like($field, $search)
    {
        $this->db->or_like($field, $search);
    }

    function limit($length, $start)
    {
        $this->db->limit($length, $start);
    }
    function order_by($field_order, $type_order='ASC')
    {
        $this->db->order_by($field_order,$type_order);
    }
    function relation($table_ref, $id, $text )
    {
        $this->db->select($id, $text);
        $data = $this->db->get($table_ref);
        return $data->result_array();
    }
}
