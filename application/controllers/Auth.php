<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model("Admin_model", "admin");
    }

    public function index()
    {
        redirect(base_url("auth/login"));
    }

    public function login()
    {
        if ($this->session->has_userdata(INNO_SESSION)) {
            redirect(base_url("dashboard"));
        }
        $this->loadView('auth/login2');
    }

    public function proses_login()
    {
        $username   = $this->input->post('username', TRUE);
        $password   = md5($this->input->post('password', TRUE));

        $cekLogin   = $this->admin->where([
            "username_admin"    => $username,
            "password_admin"    => $password
        ])->with_toko()->get();

        if ($cekLogin) {
            $this->session->set_userdata(INNO_SESSION, $cekLogin);            
            redirect(base_url("dashboard"));
        } else {
            $this->session->set_flashdata("gagal", "Username atau password yang anda masukan salah!");
            redirect(base_url("auth/login"));
        }
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect(base_url());
    }

    public function cobaKirimNotif(){
        $dataNotifikasi = array(
            "title"     => "Rafli Ganteng Banget",
            "message"   => "Rafli Firdausy Irawan tesss",            
            // "image"     => "https://d1hlpam123zqko.cloudfront.net/959/643/138/-69996988-1thandm-hk11f2rq56hfrna/original/avatar.jpg"
        );
        $data = array(
            // 'to' => "/topics/global",
            'to' => "cx28rD-wRTqPCRv-XObTSu:APA91bE5rQL4uD-3IrwzevFIn2xFMSNn4QeoElxZL7mL3maDzrMQYTYNnDMaZ4RmoZ7Osekqw8iYWxqriggsesP2DiPpdw-fpxd9vZAH51OBzxqwF0TeyM7UFV54FmadXHcIL-Dx5PYF",
            'data' => $dataNotifikasi,
        );
        echo sendPushNotification($data);
    }
}
