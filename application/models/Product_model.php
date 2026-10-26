<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Product_model extends CI_Model
{
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    function view_products()
    {      
      return $this->db->get('products');
    }
    function insert_products($data)
    {
        return $this->db->insert("products",$data);
    }

    function get_products($id=null)
    {
        if($id)
        {
            return $this->db->get_where('products',["id"=>$id])->row_array();
        }
        return $this->db->get("products")->result_array();
    }
    function edit_products($id,$data)
    {
        $this->db->where("id",$id);
        return $this->db->update("products",$data);
    }
    function delete_products($id)
    {
        return $this->db->delete('products',["id"=>$id]);
    }
    function total_product()
    {       
        $total = $this->db->count_all_results('products');
        return $total;
    }
}
