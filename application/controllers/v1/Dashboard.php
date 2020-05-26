<?php
defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;

class Dashboard extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model("Banner_model", "banner");
        $this->load->model("Toko_model", "mitra");
    }

    public function banner_get()
    {
        $dataBanner = $this->banner->where(["status_banner" => 1])->get_all();
        if ($dataBanner) {
            for ($i = 0; $i < sizeof($dataBanner); $i++) {
                $dataBanner[$i]["foto_banner"] = asset("banner/" . $dataBanner[$i]["foto_banner"]);
            }
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_OK,
                "response_message"      => "Banner ditemukan",
                "data"                  => $dataBanner
            ), REST_Controller::HTTP_OK);
        } else {
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_NOT_FOUND,
                "response_message"      => "Banner tidak ditemukan",
                "data"                  => null
            ), REST_Controller::HTTP_OK);
        }
    }

    public function mitra_get()
    {
        $idMitra = $this->input->get("id_mitra", TRUE);
        $kondisi = [];
        if (!empty($idMitra)) {
            $kondisi = [
                "id_toko"              => $idMitra,
                "latitude_toko !="     => null,
                "longitude_toko !="    => null
            ];
        } else {
            $kondisi = [
                "latitude_toko !="     => null,
                "longitude_toko !="    => null
            ];
        }

        $mitra = $this->mitra
            ->where($kondisi)
            ->get_all();
        
        if ($mitra) {
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_OK,
                "response_message"      => "Mitra ditemukan",
                "data"                  => $mitra
            ), REST_Controller::HTTP_OK);
        } else {
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_NOT_FOUND,
                "response_message"      => "Mitra tidak ditemukan",
                "data"                  => null
            ), REST_Controller::HTTP_OK);
        }
    }
}
