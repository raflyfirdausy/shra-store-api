<?php

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;

class Transaksi extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model("Transaksi_model", "transaksi");
    }

    public function riwayat_get()
    {
        $id_user = $this->input->get("id_user", TRUE);
        $id_transaksi   = $this->input->get("id_transaksi", TRUE);

        if (isset($id_transaksi)) {
            $kondisi = ["id_transaksi" => $id_transaksi];
        } else {
            $kondisi = ["id_user" => $id_user];
        }

        $listTransaksi = $this->transaksi
            ->where($kondisi)
            ->where("status_transaksi", "=", SELESAI_TERKIRIM, FALSE)
            ->where("status_transaksi", "=", DIBATALKAN, TRUE)
            ->with_user()
            ->with_toko()
            ->with_detail_transaksi(["with"  => ["relation"  => "barang"]])
            ->order_by("updated_at", "DESC")
            ->get_all();

        if ($listTransaksi) {
            for ($i = 0; $i < sizeof($listTransaksi); $i++) {
                if ($listTransaksi[$i]["status_transaksi"] == SELESAI_TERKIRIM) {
                    $listTransaksi[$i]["keterangan_transaksi"] = "Pesanan sudah terkirim";
                } else if ($listTransaksi[$i]["status_transaksi"] == DIBATALKAN) {
                    $listTransaksi[$i]["keterangan_transaksi"] = "Pesanan dibatalkan";
                } else {
                    $listTransaksi[$i]["keterangan_transaksi"] = "Terjadi kesalahan pada status transaksi";
                }

                if ($listTransaksi[$i]["detail_transaksi"]) {
                    for ($x = 0; $x < sizeof($listTransaksi[$i]["detail_transaksi"]); $x++) {
                        $listTransaksi[$i]["detail_transaksi"][$x]["barang"]["foto_barang"] = asset("barang/" . $listTransaksi[$i]["detail_transaksi"][$x]["barang"]["foto_barang"]);
                    }
                }
            }
        }

        if ($listTransaksi) {
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_OK,
                "response_message"      => "Transaksi berhasil ditemukan",
                "data"                  => $listTransaksi
            ), REST_Controller::HTTP_OK);
        } else {
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_EXPECTATION_FAILED,
                "response_message"      => "Transaksi tidak ditemukan",
                "data"                  => NULL
            ), REST_Controller::HTTP_OK);
        }
    }

    public function pesanan_get()
    {
        $id_user        = $this->input->get("id_user", TRUE);
        $id_transaksi   = $this->input->get("id_transaksi", TRUE);

        if (isset($id_transaksi)) {
            $kondisi = ["id_transaksi" => $id_transaksi];
        } else {
            $kondisi = ["id_user" => $id_user];
        }

        $listTransaksi = $this->transaksi
            ->where($kondisi)
            ->where("status_transaksi", "=", BELUM_DIPROSES, FALSE)
            ->where("status_transaksi", "=", SEDANG_DIPROSES, TRUE)
            ->with_user()
            ->with_toko()
            ->with_detail_transaksi(["with"  => ["relation"  => "barang"]])
            ->order_by("updated_at", "DESC")
            ->get_all();

        if ($listTransaksi) {
            for ($i = 0; $i < sizeof($listTransaksi); $i++) {
                if ($listTransaksi[$i]["status_transaksi"] == BELUM_DIPROSES) {
                    $listTransaksi[$i]["keterangan_transaksi"] = "Menunggu di proses";
                } else if ($listTransaksi[$i]["status_transaksi"] == SEDANG_DIPROSES) {
                    $listTransaksi[$i]["keterangan_transaksi"] = "Pesanan sedang diproses";
                } else {
                    $listTransaksi[$i]["keterangan_transaksi"] = "Terjadi kesalahan pada status transaksi";
                }

                if ($listTransaksi[$i]["detail_transaksi"]) {
                    for ($x = 0; $x < sizeof($listTransaksi[$i]["detail_transaksi"]); $x++) {
                        $listTransaksi[$i]["detail_transaksi"][$x]["barang"]["foto_barang"] = asset("barang/" . $listTransaksi[$i]["detail_transaksi"][$x]["barang"]["foto_barang"]);
                    }
                }
            }
        }

        if ($listTransaksi) {
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_OK,
                "response_message"      => "Pesanan berhasil ditemukan",
                "data"                  => $listTransaksi
            ), REST_Controller::HTTP_OK);
        } else {
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_EXPECTATION_FAILED,
                "response_message"      => "Pesanan tidak ditemukan",
                "data"                  => NULL
            ), REST_Controller::HTTP_OK);
        }
    }

    public function proses_post()
    {
        $data                   = json_decode(file_get_contents("php://input"));
        $id_user                = $data->id_user;
        $id_toko                = $data->id_toko;
        $alamat_transaksi       = $data->alamat_transaksi;
        $alamatdetail_transaksi = $data->alamatdetail_transaksi;
        $nohp_transaksi         = $data->nohp_transaksi;
        $ongkir_transaksi       = $data->ongkir_transaksi;
        $status_transaksi       = BELUM_DIPROSES;
        $kode_transaksi         = "B" . $id_user .  now(); //? B{id_user}{UNIX timestamp}
        $keranjang              = $data->keranjang;

        //TODO : INSERT INTO TABLE TRANSAKSI
        $dataInsertTransaksi    = [
            "kode_transaksi"            => $kode_transaksi,
            "id_user"                   => $id_user,
            "id_toko"                   => $id_toko,
            "alamat_transaksi"          => $alamat_transaksi,
            "alamatdetail_transaksi"    => $alamatdetail_transaksi,
            "alamatdetail_transaksi"    => $alamatdetail_transaksi,
            "nohp_transaksi"            => $nohp_transaksi,
            "ongkir_transaksi"          => $ongkir_transaksi,
            "status_transaksi"          => $status_transaksi
        ];

        $insertTransaksi = $this->transaksi->insert($dataInsertTransaksi);
        if ($insertTransaksi) {
            $id_transaksi = $insertTransaksi;
            $dataInsertDetailTransaksi = [];
            for ($i = 0; $i < sizeof($keranjang); $i++) {
                $dataItemDetailTransaksi = [
                    "id_transaksi"          => $id_transaksi,
                    "id_barang"             => $keranjang[$i]->dataBean->id_barang,
                    "hargabeli_barang"      => NULL,
                    "hargajual_barang"      => $keranjang[$i]->dataBean->hargajual_barang,
                    "hargadiskon_barang"    => isset($keranjang[$i]->dataBean->hargadiskon_barang) ? $keranjang[$i]->dataBean->hargadiskon_barang : null,
                    "banyak_barang"         => $keranjang[$i]->banyak,
                    "catatan_barang"        => NULL
                ];
                array_push($dataInsertDetailTransaksi, $dataItemDetailTransaksi);
            }

            //TODO : INSERT DETAIL TRANSAKSI AND GET DATA DETAIL INSERT BATCH
            $this->db->insert_batch("detail_transaksi", $dataInsertDetailTransaksi);
            $detailTransaksi = $this->transaksi
                ->where(["id_transaksi" => $id_transaksi])
                ->with_user()
                ->with_toko()
                ->with_detail_transaksi(["with"  => ["relation"  => "barang"]])
                ->get();

            if ($detailTransaksi) {
                for ($i = 0; $i < sizeof($detailTransaksi["detail_transaksi"]); $i++) {
                    $detailTransaksi["detail_transaksi"][$i]["barang"]["foto_barang"] = asset("barang/" . $detailTransaksi["detail_transaksi"][$i]["barang"]["foto_barang"]);
                }
            }

            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_OK,
                "response_message"      => "Transaksi berhasil, silahkan lihat status transaksi pada menu pesanan",
                "data"                  => $detailTransaksi
            ), REST_Controller::HTTP_OK);
        } else {
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_EXPECTATION_FAILED,
                "response_message"      => "Transaksi gagal, mohon coba beberapa saat lagi",
                "data"                  => NULL
            ), REST_Controller::HTTP_OK);
        }
    }

    public function batalkan_post()
    {
        $id_transaksi   = $this->input->post("id_transaksi");
        $cekTransaksi = $this->transaksi
            ->with_user()
            ->with_toko()
            ->with_detail_transaksi(["with"  => ["relation"  => "barang"]])
            ->get($id_transaksi);

        if (!empty($id_transaksi)) {
            if ($cekTransaksi) {
                $update         = $this->transaksi->update(["status_transaksi" => DIBATALKAN], $id_transaksi);
                if ($update) {
                    return $this->response(array(
                        "status"                => true,
                        "response_code"         => REST_Controller::HTTP_OK,
                        "response_message"      => "Pesanan berhasil dibatalkan",
                        "data"                  => $cekTransaksi
                    ), REST_Controller::HTTP_OK);
                } else {
                    return $this->response(array(
                        "status"                => true,
                        "response_code"         => REST_Controller::HTTP_EXPECTATION_FAILED,
                        "response_message"      => "Pesanan gagal dibatalkan, cobalah beberapa saat lagi",
                        "data"                  => NULL
                    ), REST_Controller::HTTP_OK);
                }
            } else {
                return $this->response(array(
                    "status"                => true,
                    "response_code"         => REST_Controller::HTTP_BAD_REQUEST,
                    "response_message"      => "Pesanan tidak ditemukan",
                    "data"                  => NULL
                ), REST_Controller::HTTP_OK);
            }
        } else {
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_BAD_REQUEST,
                "response_message"      => "Parameter yang dikirimkan tidak lengkap",
                "data"                  => NULL
            ), REST_Controller::HTTP_OK);
        }
    }
}
