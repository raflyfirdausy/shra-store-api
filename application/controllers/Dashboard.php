<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends BaseAdmin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Toko_model', 'toko');
        $this->load->model('Admin_model', 'admin');
        $this->load->model('Barang_model', 'barang');
        $this->load->model('Transaksi_model', 'transaksi');
        $this->load->model('User_model', 'user');
        $this->load->model('Master_barang_model', 'master_barang');
        $this->load->model('Kategori_barang_model', 'kategori_barang');
        $this->load->model('Detail_transaksi_model', 'detail_transaksi');
    }

    public function index()
    {
        $level = $this->userData->level_admin;
        if ($level == LEVEL_SUPER_ADMIN) {
            $jumlahSelesai = $this->transaksi
                ->where('status_transaksi', SELESAI_TERKIRIM)
                ->as_object()
                ->count_rows();
            $jumlahBatal = $this->transaksi
                ->where('status_transaksi', DIBATALKAN)
                ->as_object()
                ->count_rows();
            $totalTrans = $this->transaksi->as_object()->count_rows();
            $jumlahUser = $this->user->as_object()->count_rows();
            $jumlahBarang = $this->master_barang->as_object()->count_rows();
            $TransBaru = $this->transaksi
                ->with_user()
                ->with_toko()
                ->with_detail_transaksi(["with"  => ["relation"  => "barang"]])
                ->order_by('created_at', 'asc')
                ->limit(5)
                ->as_object()
                ->get_all();
            $barangBaru = $this->master_barang
                ->with_kategori()
                ->order_by('created_at', 'desc')
                ->limit(5)
                ->as_object()
                ->get_all();
            $transaksi_sebulan = $this->transaksi
                ->with_user()
                ->with_toko()
                ->with_detail_transaksi(["with"  => ["relation"  => "barang"]])
                ->where('created_at >=', date('Y-m-d', time() - 30 * 3600 * 24))
                ->where('created_at <=', date('Y-m-d'))
                ->order_by('created_at')
                ->as_object()
                ->get_all();
            $banyak_trans_sebulan = $this->transaksi
                ->where('created_at >=', date('Y-m-d', time() - 30 * 3600 * 24))
                ->where('created_at <=', date('Y-m-d'))
                ->count_rows();
            $trans_diproses_sebulan = $this->countedMonth(SEDANG_DIPROSES);
            $trans_selesai_sebulan = $this->countedMonth(SELESAI_TERKIRIM);
            $trans_batal_sebulan = $this->countedMonth(DIBATALKAN);
            $transaksiToko = $this->transaksi
                ->where("status_transaksi", SELESAI_TERKIRIM)
                ->as_object()
                ->get_all();
            $arrayIdTransaksi = [];
            if ($transaksiToko) {
                foreach ($transaksiToko as $transaksi) {
                    array_push($arrayIdTransaksi, $transaksi->id_transaksi);
                }
                $terlaris = $this->detail_transaksi
                    ->fields()->select_sum("banyak_barang")
                    ->with_barang()
                    ->where("id_transaksi", $arrayIdTransaksi)
                    ->group_by("id_barang")
                    ->order_by("SUM(banyak_barang)", "DESC")
                    ->limit(5)
                    ->as_object()
                    ->get_all();
            }
        } elseif ($level == LEVEL_ADMIN) {
            $jumlahSelesai = $this->transaksi
                ->where('id_toko', $this->userData->id_toko)
                ->where('status_transaksi', SELESAI_TERKIRIM)
                ->as_object()
                ->count_rows();
            $jumlahBatal = $this->transaksi
                ->where('id_toko', $this->userData->id_toko)
                ->where('status_transaksi', DIBATALKAN)
                ->as_object()
                ->count_rows();
            $jumlahUser = $this->user->as_object()->count_rows();
            $jumlahBarang = $this->barang->where('id_toko', $this->userData->id_toko)
                ->as_object()->count_rows();
            $TransBaru = $this->transaksi
                ->with_user()
                ->with_toko()
                ->with_detail_transaksi(["with"  => ["relation"  => "barang"]])
                ->where('id_toko', $this->userData->id_toko)
                ->order_by('created_at', 'desc')
                ->limit(5)
                ->as_object()
                ->get_all();
            $barangBaru = $this->barang
                ->with_kategori()
                ->where('id_toko', $this->userData->id_toko)
                ->order_by('created_at', 'asc')
                ->limit(5)
                ->as_object()
                ->get_all();
            $totalTrans = $this->transaksi
                ->with_user()
                ->with_toko()
                ->with_detail_transaksi(["with"  => ["relation"  => "barang"]])
                ->where('id_toko', $this->userData->id_toko)
                ->as_object()
                ->count_rows();
            $transaksi_sebulan = $this->transaksi
                ->with_user()
                ->with_toko()
                ->with_detail_transaksi(["with"  => ["relation"  => "barang"]])
                ->where('id_toko', $this->userData->id_toko)
                ->where('created_at >=', date('Y-m-d', time() - 30 * 3600 * 24))
                ->where('created_at <=', date('Y-m-d'))
                ->order_by('created_at')
                ->as_object()
                ->get_all();
            $banyak_trans_sebulan = $this->transaksi
                ->where('id_toko', $this->userData->id_toko)
                ->where('created_at >=', date('Y-m-d', time() - 30 * 3600 * 24))
                ->where('created_at <=', date('Y-m-d'))
                ->count_rows();
            $trans_diproses_sebulan = $this->countedMonth_admin(SEDANG_DIPROSES);
            $trans_selesai_sebulan = $this->countedMonth_admin(SELESAI_TERKIRIM);
            $trans_batal_sebulan = $this->countedMonth_admin(DIBATALKAN);
            $transaksiToko = $this->transaksi
                ->where([
                    "id_toko"           => $this->userData->id_toko,
                    "status_transaksi"  => SELESAI_TERKIRIM
                ])
                ->as_object()
                ->get_all();
            $arrayIdTransaksi = [];
            if ($transaksiToko) {
                foreach ($transaksiToko as $transaksi) {
                    array_push($arrayIdTransaksi, $transaksi->id_transaksi);
                }
                $terlaris = $this->detail_transaksi
                    ->fields()->select_sum("banyak_barang")
                    ->with_barang()
                    ->where("id_transaksi", $arrayIdTransaksi)
                    ->group_by("id_barang")
                    ->order_by("SUM(banyak_barang)", "DESC")
                    ->limit(5)
                    ->as_object()
                    ->get_all();
            }
        }
        $isi = [
            'jumlahTrans' => $jumlahSelesai,
            'jumlahBatal' => $jumlahBatal,
            'jumlahUser' => $jumlahUser,
            'jumlahBarang' => $jumlahBarang,
            'TransBaru' => $TransBaru,
            'barangBaru' => $barangBaru,
            'totalTrans' => $totalTrans,
            'transaksi_sebulan' => $transaksi_sebulan,
            'banyak_trans_sebulan' => $banyak_trans_sebulan,
            'trans_selesai_sebulan' => $trans_selesai_sebulan,
            'trans_batal_sebulan' => $trans_batal_sebulan,
            'trans_diproses_sebulan' => $trans_diproses_sebulan,
            'level' => $level,
        ];
        !empty($terlaris) ? $isi['terlaris'] = $terlaris : "";
        $this->loadViewAdmin("dashboard/index", $isi);
    }

    public function countedMonth_admin($status = BELUM_DIPROSES)
    {
        $getData = $this->transaksi
            ->where('id_toko', $this->userData->id_toko)
            ->where('created_at >=', date('Y-m-d', time() - 30 * 3600 * 24))
            ->where('created_at <=', date('Y-m-d'))
            ->where('status_transaksi', $status)
            ->count_rows();
        return $getData;
    }

    public function countedMonth($status = BELUM_DIPROSES)
    {
        $getData = $this->transaksi
            ->where('created_at >=', date('Y-m-d', time() - 30 * 3600 * 24))
            ->where('created_at <=', date('Y-m-d'))
            ->where('status_transaksi', $status)
            ->count_rows();
        return $getData;
    }
}
