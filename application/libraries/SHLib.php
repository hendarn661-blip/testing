<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
   class SHLib_fieldset{
    public $myfieldslist ;
    public $myfieldtype;
    public $myfields;
    public $myfillfield;
    public $basic_model;
    public $form_control;


    public $mytable ;
    public $myview ;
    public $myititle ;
    public $mysubject;
    public $myorder ;
    protected $CI;

    public function __construct() {
        // Assign the CodeIgniter super object to $this->CI
        $this->CI =& get_instance();
        // $this->CI->load->library('session');
    }
    
    protected function set_default_Model()
    {
      $this->CI->load->model('SHLib_model');
      $this->basic_model = new SHLib_model();
    }

    public function fill_data($fieldname)
    {
        $data = $this->basic_model->relation($table_ref, $id, $fieldname);
        $write_field="<select id='field-$fieldname' name='$fieldname'>";
        foreach ($data as $key ) {
          $write_field.="<option value='$key[$id]'>$key[$fieldname]</option>";
        }
        $write_field.="</select>";
        $this->myfillfield=$write_field;
            
    }
    public function set_fields_list($fields)
    {
      $title=array();
      foreach ($fields as $name=>$type) {
        $title[] = str_replace("_"," ",$name);
      }
      $title[] = "Action";
      $this->myfieldslist = $title;
    }
    public function set_fields($fields)
    {
      $this->myfields = $fields;      
      $write_field='';
      foreach ($fields as $name => $type) {
        $label = str_replace("_"," ",$name);
        $write_field.='<label for="'.$name.'">'.$label.'</label>';
        switch ($type) {
          case 'textarea':
            $write_field.='<textarea id="field-'.$name.'" name="'.$name.'" placeholder="'.$label.'.."></textarea>';
            break;
          case 'select':
            $write_field.=$this->myfillfield;           
          default:
            # code...
            $write_field.='<input type="'.$type.'" id="field-'.$name.'" name="'.$name.'" placeholder="'.$label.'..">';
            break;
        }
      }
      $this->form_control =  $write_field;
    }
  
   }
   class SHLib_forms extends SHLib_fieldset{
     public function create_form_add($subject, $form_control)
     {
        $strhtml = "<div id=\"dialog\" title=\"Add ". $subject."\">
          <form action=\"\" id=\"form-add\">".
            $form_control."				
          </form>
        </div>";
        return $strhtml;
     }

     public function create_form_edit($subject, $form_control)
     {
        $strhtml = "<div id=\"dialog_edit\" title=\"Edit ". $subject."\">
          <form action=\"\" id=\"form-edit\">".
            $form_control."				
          </form>
        </div>";
        return $strhtml;
     }
   }
   class SHLib extends SHLib_forms{
    
      public function set_table($tablename) {
        $this->mytable = $tablename;       
      }
     
      public function order_by($field, $type){
        $this->myorder=array($field,$type);     
      }
      public function set_subject($subject)
      {
          $this->mysubject=$subject;
      }
      public function set_title($title)
      {
        $this->mytitle=$title;
      }
      public function set_view($viewname)
      {         
        // Memuat view        
        $this->myview = $viewname;
        $data['title'] = $this->mytitle;
        $data['fieldslist']  = $this->myfieldslist;
        $data['subject'] = $this->mysubject;
        $data['fields'] = $this->myfields;
        $data['form_control'] = $this->form_control;
        $data['form_add'] = $this->create_form_add($this->mysubject, $this->form_control);
        $data['form_edit'] = $this->create_form_edit($this->mysubject, $this->form_control);
        // $data['keys'] = $this->get_table();
        $data['table'] = $this->mytable;
        $data[$this->mytable] = $this->basic_model->get_data($this->mytable);
        $this->CI->load->view("header");
        $this->CI->load->view($viewname, $data);  
        $this->CI->load->view("footer");
      }
      public function pagging()
      {
          /* $draw = intval($this->input->post("draw"));
          $start = intval($this->input->post("start"));
          $length = intval($this->input->post("length")); */
          $this->render();          
          $draw = $_REQUEST["draw"];
          $start = $_REQUEST["start"];
          $length = $_REQUEST["length"];
          $tablename = $this->mytable;
          $subject = $this->mysubject;
          $order = $this->myorder;
          $search = isset($_REQUEST['search'])  ?$_REQUEST['search']['value']:'';
          $fields = array_keys($this->myfields);
          $count = $this->basic_model->total_data($tablename);
          
          $output=array();

          $output['draw'] = $draw;

          $output['recordsTotal']=$output['recordsFiltered']=$count;
          // echo $count;
          // echo $start;
          $output['data']=array();

          if($search!='')
          {
            $this->basic_model->like($fields[0], $search);
            for($i = 1 ; $i< count($fields);$i++) {              
                $this->basic_model->or_like($fields[$i], $search);
            }       
          }
          $this->basic_model->limit($length,$start);   
          if(isset($this->myorder))
            $this->basic_model->order_by($order[0], $order[1]);
          else $this->basic_model->order_by('id','DESC');
          
          $data = $this->basic_model->view_data($tablename);
          // $products = $this->product_model->view_products(null,'');
          // print_r($products);
          // $nomor_urut=$start+1;

          if($search!='')
          {
            $this->basic_model->like($fields[0], $search);
            #$this->db->or_like($fields[1], $search);
            for($i = 1 ; $i< count($fields);$i++) {               
                $this->basic_model->or_like($fields[$i], $search);
            }     
            $jum = $this->basic_model->view_data($tablename);
            $output['recordsTotal']=$output['recordsFiltered']=$jum->num_rows();
          }
          
          foreach($data->result_array() as $key) {
            $action="<a title='Edit $subject' id='btn_edit' class='action' onclick='edit(".$key['id'].")'><i class='fa fa-pencil text-success'></i></a>
            <a title='Edit ".$subject."' id='btn_hapus' class='action' onclick='hapus(".$key['id'].")'><i class='fa fa-trash text-danger'></i></a>";
            $col = array();
            for ($i=0; $i < count($fields) ; $i++) { 
              $col[]=$key[$fields[$i]];              
            }
            $col[]=$action;
            // $output['data'][]= array($key['category_name'],$key['active'],$action);
             $output['data'][]= $col;
          }
          // print_r($products);
          echo json_encode($output);       
      }
      public function render()
      {
        if($this->basic_model === null)
			  $this->set_default_Model();
      }
   }