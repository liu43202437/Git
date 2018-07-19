<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Noauth extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->output->set_status_header('404'); // setting header to 404
        $this->load->view('noauth');
    }
}
?>