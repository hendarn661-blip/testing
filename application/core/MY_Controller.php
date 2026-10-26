<?php
defined('BASEPATH') OR exit('No direct script access allowed');
#[AllowDynamicProperties]
class MY_Controller Extends CI_Controller
{
    public  function __construct()
    {
        parent::__construct();
        $this->load->model("lims_model");
        $this->load->model('registrasi_model');
        $this->load->library(["form_validation","session"]);
        $this->load->helper("url");
    }

    public function render_view($view,$data=[])
    {
        $this->load->view("header");

        $this->load->view($view,$data);

        $this->load->view("footer");

    }
}
