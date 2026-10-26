<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Category_model extends CI_Model
{
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    function view_category()
    {      
      return $this->db->get('category');
    }
    function insert_category($data)
    {
        return $this->db->insert("category",$data);
    }

    function get_category($id=null)
    {
        if($id)
        {
            return $this->db->get_where('category',["id"=>$id])->row_array();
        }
        return $this->db->get("category")->result_array();
    }
    function edit_category($id,$data)
    {
        $this->db->where("id",$id);
        return $this->db->update("category",$data);
    }
    function delete_category($id)
    {
        return $this->db->delete('category',["id"=>$id]);
    }
    function total_category()
    {       
        $total = $this->db->count_all_results('category');
        return $total;
    }
}
