<?php

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;

class Barang extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model("Barang_model", "barang");
        $this->load->model("Kategori_barang_model", "kategori_barang");
        $this->load->model("Detail_transaksi_model", "detail");
        $this->load->model("Transaksi_model", "transaksi");
    }

    public function index_get()
    {
        $id_mitra               = $this->input->get_request_header("id_mitra", TRUE);
        $id_kategori_barang     = $this->input->get("id_kategori_barang");

        if ($id_kategori_barang == "") {
            $kondisi = ["id_toko" => $id_mitra];
        } else {
            if ($id_kategori_barang == "0") {
                $kondisi = ["id_toko" => $id_mitra, "id_kategori_barang" => null];
            } else {
                $kondisi = ["id_toko" => $id_mitra, "id_kategori_barang" => $id_kategori_barang];
            }
        }

        $barang = $this->barang
            ->where($kondisi)
            ->order_by("id_barang", "RANDOM")
            ->get_all();

        if ($barang) {
            for ($i = 0; $i < sizeof($barang); $i++) {
                $barang[$i]["foto_barang"] = asset("barang/" . $barang[$i]["foto_barang"]);
            }
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_OK,
                "response_message"      => "Barang ditemukan",
                "minimal_data"          => 5,
                "data"                  => $barang,
            ), REST_Controller::HTTP_OK);
        } else {
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_NOT_FOUND,
                "response_message"      => "Barang tidak ditemukan",
                "minimal_data"          => 5,
                "data"                  => null,
            ), REST_Controller::HTTP_OK);
        }
    }

    public function kategori_get()
    {
        $dataKategori = $this->kategori_barang->get_all();
        if ($dataKategori) {
            for ($i = 0; $i < sizeof($dataKategori); $i++) {
                if (empty($dataKategori[$i]["foto_kategori_barang"])) {
                    $dataKategori[$i]["foto_kategori_barang"] =  asset("kategori_barang/default.svg");
                } else {
                    $dataKategori[$i]["foto_kategori_barang"] =  asset("kategori_barang/" . $dataKategori[$i]["foto_kategori_barang"]);
                }
            }
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_OK,
                "response_message"      => "Kategori barang ditemukan",
                "data"                  => $dataKategori,
            ), REST_Controller::HTTP_OK);
        } else {
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_NOT_FOUND,
                "response_message"      => "Kategori barang tidak ditemukan",
                "data"                  => null,
            ), REST_Controller::HTTP_OK);
        }
    }

    public function terlaris_get()
    {
        $id_mitra = $this->input->get_request_header("id_mitra", TRUE);

        $transaksiToko = $this->transaksi
            ->where([
                "id_toko"           => $id_mitra,
                "status_transaksi"  => SELESAI_TERKIRIM
            ])
            ->as_object()
            ->get_all();

        $arrayIdTransaksi = [];
        if ($transaksiToko) {
            foreach ($transaksiToko as $transaksi) {
                array_push($arrayIdTransaksi, $transaksi->id_transaksi);
            }
            $terlaris = $this->detail
                ->fields()->select_sum("banyak_barang")
                ->where("id_transaksi", $arrayIdTransaksi)
                ->with_barang()
                ->group_by("id_barang")
                ->order_by("SUM(banyak_barang)", "DESC")
                ->limit(10)
                ->get_all();

            if ($terlaris) {
                for ($i = 0; $i < sizeof($terlaris); $i++) {
                    $terlaris[$i]["barang"]["foto_barang"] = asset("barang/" . $terlaris[$i]["barang"]["foto_barang"]);
                }
                return $this->response(array(
                    "status"                => true,
                    "response_code"         => REST_Controller::HTTP_OK,
                    "response_message"      => "Barang terlaris ditemukan",
                    "data"                  => $terlaris,
                ), REST_Controller::HTTP_OK);
            } else {
                return $this->response(array(
                    "status"                => true,
                    "response_code"         => REST_Controller::HTTP_EXPECTATION_FAILED,
                    "response_message"      => "Barang terlaris tidak ditemukan",
                    "data"                  => null,
                ), REST_Controller::HTTP_OK);
            }
        } else {
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_NOT_FOUND,
                "response_message"      => "Barang terlaris tidak ditemukan",
                "data"                  => null,
            ), REST_Controller::HTTP_OK);
        }
    }

    public function terlaris_backup_get()
    {
        $id_mitra        = $this->input->get_request_header("id_mitra", TRUE);
        $barang = $this->barang
            ->where(["id_toko" => $id_mitra])
            ->order_by("id_barang", "RANDOM")
            ->get_all();

        if ($barang) {
            for ($i = 0; $i < sizeof($barang); $i++) {
                $barang[$i]["foto_barang"] = asset("barang/" . $barang[$i]["foto_barang"]);
            }
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_OK,
                "response_message"      => "Barang ditemukan",
                "minimal_data"          => 5,
                "data"                  => $barang,
            ), REST_Controller::HTTP_OK);
        } else {
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_NOT_FOUND,
                "response_message"      => "Barang tidak ditemukan",
                "minimal_data"          => 5,
                "data"                  => null,
            ), REST_Controller::HTTP_OK);
        }
    }

    public function get_keranjang_post()
    {
        $arrayIdBarang      = $this->input->post("arrayIdBarang", TRUE);
        $id_mitra           = $this->input->get_request_header("id_mitra", TRUE);

        $arrayIdBarang = json_decode($arrayIdBarang);
        $barang = $this->barangMitra
            ->with_mitra()
            ->with_barang()
            ->where("stok_barangmitra >", 0)
            ->where("id_mitra", $id_mitra)
            ->where("id_barang", $arrayIdBarang)
            ->get_all();

        if (empty($arrayIdBarang)) {
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_BAD_REQUEST,
                "response_message"      => "Tidak ada barang dalam keranjang",
                "data"                  => null,
            ), REST_Controller::HTTP_OK);
        } else if (empty($id_mitra)) {
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_BAD_REQUEST,
                "response_message"      => "Mitra tidak diketahui",
                "data"                  => null,
            ), REST_Controller::HTTP_OK);
        }

        if ($barang) {
            for ($i = 0; $i < sizeof($barang); $i++) {
                $barang[$i]["barang"]["foto_barang"] = asset("barang/" . $barang[$i]["barang"]["foto_barang"]);
            }
        }
        return $this->response(array(
            "status"                => true,
            "response_code"         => REST_Controller::HTTP_OK,
            "response_message"      => "Data keranjang ditemukan",
            "data"                  => $barang,
        ), REST_Controller::HTTP_OK);
    }

    public function promo_get()
    {
        $id_mitra                   = $this->input->get_request_header("id_mitra", TRUE);
        $kondisi = [
            "id_toko"               => $id_mitra,
            "hargadiskon_barang !=" => NULL,
            "hargajual_barang >"    => "hargadiskon_barang"
        ];

        $barang = $this->barang
            ->where($kondisi)
            ->order_by("id_barang", "RANDOM")
            ->get_all();

        if ($barang) {
            for ($i = 0; $i < sizeof($barang); $i++) {
                $barang[$i]["foto_barang"] = asset("barang/" . $barang[$i]["foto_barang"]);
            }
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_OK,
                "response_message"      => "Barang ditemukan",
                "minimal_data"          => 50,
                "data"                  => $barang,
            ), REST_Controller::HTTP_OK);
        } else {
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_NOT_FOUND,
                "response_message"      => "Nantikan promo di waktu yang lain.",
                "minimal_data"          => 50,
                "data"                  => null,
            ), REST_Controller::HTTP_OK);
        }
    }
}
