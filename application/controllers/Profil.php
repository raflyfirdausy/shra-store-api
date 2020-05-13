<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Profil extends BaseAdmin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Admin_model', 'admin');
        $this->load->model('Toko_model', 'toko');
    }

    public function index($key = null)
    {
        $key = $this->userData->id_admin;
        $getData = $this->admin->with_toko()->get($key);
        if (!$getData) { // Kalo datane ga ada, antisipasi inject URL
            redirect(base_url("dashboard"));
        }

        $isi['data']      = $getData;
        $this->loadViewAdmin("profil/index", $isi);
    }

    public function proses_edit_profil()
    {
        $id_admin = $this->userData->id_admin;
        $pass = "";
        $getData = $this->admin->with_toko()->get($id_admin);
        if (!$getData) { // Kalo datane ga ada, antisipasi inject URL
            redirect(base_url("dashboard"));
        }
        if ($this->input->post('password_admin_baru') != "") {
            $pass  = md5($this->input->post('password_admin_baru'));
        } else {
            $pass  = $this->input->post('password_admin');
        }
        $dataUpdate     = [
            "nama_admin"         => $this->input->post('nama_admin'),
            "username_admin"     => $this->input->post('username_admin'),
            "password_admin"     => $pass
        ];

        $update = $this->admin->update($dataUpdate, $id_admin);
        if ($update) {
            $this->session->set_userdata(INNO_SESSION, $dataUpdate);
            $this->session->set_flashdata('sukses', "Profil Anda berhasil di update");
        } else {
            $this->session->set_flashdata('gagal', "Profil Anda gagal di update");
        }
        redirect('profil');
    }
}
