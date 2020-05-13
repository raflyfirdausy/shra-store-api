<?php

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;

class Notifikasi extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model("Notifikasi_model", "notifikasi");        
    }

    public function index_get()
    {                
        $id_user     = $this->input->get("id_user");              
        $notifikasi = $this->notifikasi
            ->where(["id_user" => $id_user])
            ->order_by("id_notifikasi", "DESC")
            ->limit(15)
            ->get_all();

        if ($notifikasi) {          
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_OK,
                "response_message"      => "Notifikasi ditemukan",                
                "data"                  => $notifikasi,
            ), REST_Controller::HTTP_OK);
        } else {
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_NOT_FOUND,
                "response_message"      => "Notifikasi tidak ditemukan",                
                "data"                  => null,
            ), REST_Controller::HTTP_OK);
        }
    }

}
