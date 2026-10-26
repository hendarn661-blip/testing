<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Lims_model extends CI_Model
{
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    private $_table_name;
    private $_primary_key;

    public function set_table($table_name)
    {
        $this->_table_name = $table_name;
    }
    public function set_primary_key($key)
    {
        $this->_primary_key = $key;
    }
    function view($where=null)
    {     
      if($where) $this->db->where($where);
    
      return $this->db->get($this->_table_name); //OR die("Please use set_table function for set Table Name");
    }
    function view_with_join($sql)
    {
        return $this->db->query($sql);
    }
    function insert($data)
    {
        return $this->db->insert($this->_table_name,$data) OR die("Please use set_table function for set Table Name");
    }
    function get_id_insert()
    {
        return $this->db->insert_id();
    }

    function get($where=null, $select=null, $table_name=null)
    {
        $__table_name = $table_name ? $table_name : $this->_table_name;

        if($select) $this->db->select($select);
        if($where)
        {
            return $this->db->get_where($__table_name,$where)->row_array();// OR die("Please use set_table function for set Table Name");
        }
        return $this->db->get($__table_name)->result_array(); //OR die("Please use set_table function for set Table Name");
    }
    function edit($id,$data)
    {
        $this->db->where($this->_primary_key,$id);
        return $this->db->update($this->_table_name,$data) OR die("Please use set_table function for set Table Name");
    }
    function delete($id)
    {
        return $this->db->delete($this->_table_name,[$this->_primary_key=>$id]) OR die("Please use set_table function for set Table Name");
    }
    function total($where=null)
    {       

        if($where) $this->db->where($where);
        $total = $this->db->count_all_results($this->_table_name); //OR die("Please use set_table function for set Table Name");
        return $total;
    }
    function total_query($query)
    {
       $query =  $this->db->query($query);
       $total = $query->num_rows();
       return $total;

    }

    public function get_enum_values($table, $field)
    {
        // Execute raw SQL to fetch the column metadata
        $query = $this->db->query("SHOW COLUMNS FROM {$table} LIKE '{$field}'");
        $row = $query->row();

        if ($row) {
            // Extract the strings inside enum('value1','value2')
            preg_match_all("/'(.*?)'/", $row->Type, $matches);
            
            if (!empty($matches[1])) {
                // Returns an associative array suitable for form dropdowns
                return array_combine($matches[1], $matches[1]);
            }
        }

        return array();
    }
}
