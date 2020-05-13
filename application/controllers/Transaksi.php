<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Transaksi extends BaseAdmin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Toko_model', 'toko');
        $this->load->model('Admin_model', 'admin');
        $this->load->model('Barang_model', 'barang');
        $this->load->model('Transaksi_model', 'transaksi');
        $this->load->model('Detail_transaksi_model', 'detail_transaksi');
    }

    public function getData($status = BELUM_DIPROSES)
    {
        $getData = $this->transaksi
            ->with_user()
            ->with_toko()
            ->with_detail_transaksi(["with"  => ["relation"  => "barang"]])
            ->where('id_toko', $this->userData->id_toko)
            ->where('status_transaksi', $status)
            ->as_object()
            ->get_all();
        return $getData;
    }

    public function pesanan_masuk($key = null)
    {
        $getData = $this->getData(BELUM_DIPROSES);
        $isi['data'] = $getData;
        $this->loadViewAdmin("transaksi/pesanan", $isi);
    }

    public function pesanan_diproses($key = null)
    {
        $getData = $this->getData(SEDANG_DIPROSES);
        $isi['data'] = $getData;
        $this->loadViewAdmin("transaksi/pesanan", $isi);
    }

    public function pesanan_selesai($key = null)
    {
        $getData = $this->getData(SELESAI_TERKIRIM);
        $isi['data'] = $getData;
        $this->loadViewAdmin("transaksi/pesanan", $isi);
    }

    public function pesanan_dibatalkan($key = null)
    {
        $getData = $this->getData(DIBATALKAN);
        $isi['data'] = $getData;
        $this->loadViewAdmin("transaksi/pesanan", $isi);
    }

    public function detail_pesanan($key = null)
    {
        $getData = $this->transaksi
            ->with_user()
            ->with_toko()
            ->with_detail_transaksi(["with"  => ["relation"  => "barang"]])
            ->as_object()
            ->get($key);
            
        $isi['data'] = $getData;
        $this->loadViewAdmin("transaksi/detail_pesanan", $isi);
    }

    public function history($key = null)
    {
        $getData = $this->transaksi
            ->with_user()
            ->with_toko()
            ->with_detail_transaksi(["with"  => ["relation"  => "barang"]])
            ->where('id_toko', $this->userData->id_toko)
            ->where('status_transaksi', SELESAI_TERKIRIM)
            ->or_where('status_transaksi', DIBATALKAN)
            ->as_object()
            ->order_by("created_at", "DESC")
            ->get_all();
        $isi['data'] = $getData;
        $this->loadViewAdmin("transaksi/history", $isi);
    }

    public function proses_pesanan($key = null)
    {
        $key = $this->input->post('id_transaksi');
        $getData = $this->transaksi
            ->with_user()
            ->with_toko()
            ->with_detail_transaksi(["with"  => ["relation"  => "barang"]])
            ->where('id_toko', $this->userData->id_toko)
            ->where('id_transaksi', $key)
            ->as_object()
            ->get();

        if (!$getData) { // Kalo datane ga ada, antisipasi inject URL
            redirect(base_url("transaksi/pesanan_masuk"));
        }

        if ($getData->status_transaksi == BELUM_DIPROSES) {
            $dataUpdate = [
                "status_transaksi" => SEDANG_DIPROSES
            ];
            $update = $this->transaksi->update($dataUpdate, $key);
            if ($update) {
                $dataNotifikasi = array(
                    "id_user"   => $getData->user->id_user,
                    "title"     => "Yeay! pesananmu sedang kami proses 🤗",
                    "message"   => "Pesanan dengan kode transaksi " . $getData->kode_transaksi . " sedang kami proses. Silahkan ditunggu ya, driver kami segera mengantar ke alamat tujuanmu. pastikan No hp kamu selalu aktif dan siapkan uang pas ya! ☺ ",
                );
                $data = array(
                    'to' => $getData->user->token_user,
                    'data' => $dataNotifikasi,
                );
                sendPushNotification($data);
                $this->session->set_flashdata('sukses', "<p>Pemesanan telah Anda konfirmasi, harap segera proses pesanan tersebut.</p><p>Cek halaman \"Pesanan Diproses\" untuk selengkapnya</p>");
            } else {
                $this->session->set_flashdata('gagal', "Status pesanan gagal diubah");
            }
            redirect(base_url("transaksi/pesanan_masuk"));
        } elseif ($getData->status_transaksi == SEDANG_DIPROSES) {
            $dataUpdate = [
                "status_transaksi" => SELESAI_TERKIRIM
            ];
            $update = $this->transaksi->update($dataUpdate, $key);
            if ($update) {
                $dataNotifikasi = array(
                    "id_user"   => $getData->user->id_user,
                    "title"     => "Terimakasih sudah belanja menggunakan aplikasi Doomu",
                    "message"   => "Kami tunggu orderan selanjutnya ya 😍😍",
                );
                $data = array(
                    'to' => $getData->user->token_user,
                    'data' => $dataNotifikasi,
                );
                sendPushNotification($data);
                $this->session->set_flashdata('sukses', "Pesanan telah dikirimkan. Data pesanan masuk ke halaman \"Pesanan Selesai\"");
            } else {
                $this->session->set_flashdata('gagal', "Status pesanan gagal diubah");
            }
            redirect(base_url("transaksi/pesanan_diproses"));
        } else {
            $this->session->set_flashdata('gagal', "Data gagal di update");
            redirect(base_url("transaksi/pesanan_masuk"));
        }
    }

    public function tolak_pesanan($key = null)
    {
        $key = $this->input->post('id_transaksi');
        $getData = $this->transaksi
            ->with_user()
            ->with_toko()
            ->with_detail_transaksi(["with"  => ["relation"  => "barang"]])
            ->where('id_toko', $this->userData->id_toko)
            ->where('id_transaksi', $key)
            ->as_object()
            ->get();

        if (!$getData) { // Kalo datane ga ada, antisipasi inject URL
            redirect(base_url("transaksi/pesanan_masuk"));
        }

        if ($getData->status_transaksi == BELUM_DIPROSES) {
            $dataUpdate = [
                "status_transaksi" => DIBATALKAN
            ];
            $update = $this->transaksi->update($dataUpdate, $key);
            if ($update) {
                $dataNotifikasi = array(
                    "id_user"   => $getData->user->id_user,
                    "title"     => "Pesananmu telah dibatalkan oleh Admin",
                    "message"   => "Pesanan dengan kode transaksi " . $getData->kode_transaksi . " telah dibatalkan oleh Admin. Hubungi Admin jika bukan kesepakatan Anda dengan Admin",
                );
                $data = array(
                    'to' => $getData->user->token_user,
                    'data' => $dataNotifikasi,
                );
                sendPushNotification($data);
                $this->session->set_flashdata('sukses', "Pesanan dengan kode transaksi " . $getData->kode_transaksi . " telah Anda batalkan.");
            } else {
                $this->session->set_flashdata('gagal', "Status pesanan gagal diubah");
            }
            redirect(base_url("transaksi/pesanan_masuk"));
        } else {
            $this->session->set_flashdata('gagal', "Data gagal di update");
            redirect(base_url("transaksi/pesanan_masuk"));
        }
    }

    public function cekPesanan()
    {
        $getData = $this->getData(BELUM_DIPROSES);
        if ($getData) {
            echo json_encode([
                "status"    => true
            ]);
        } else {
            echo json_encode([
                "status"    => false
            ]);
        }
    }
}
