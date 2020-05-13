<?php
defined('BASEPATH') or exit('No direct script access allowed');

class BaseAdmin_Controller extends MY_Controller
{
    public $userData;
    public function __construct()
    {
        parent::__construct();
        $CI = &get_instance(); //MENGGANTI $this
        if (!$this->session->has_userdata(INNO_SESSION)) {
            redirect(base_url("auth/login"));
        }
        $CI = &get_instance();

        $this->load->model("Admin_model", "admin");
        //TODO : GET USERDATA IN HERE
        $this->userData = $this->admin->where([
            "username_admin"    => $CI->session->userdata(INNO_SESSION)['username_admin'],
            "password_admin"    => $CI->session->userdata(INNO_SESSION)['password_admin']
        ])->with_toko()->as_object()->get();
    }

    protected function loadViewAdmin($view = NULL, $local_data = array(), $asData = FALSE)
    {
        if (!file_exists(APPPATH . "views/$view" . ".php")) {
            show_404();
        }
        $this->loadView("template/header", $local_data, $asData);
        $this->loadView("template/sidebar", $local_data, $asData);
        $this->loadView($view, $local_data, $asData);
        $this->loadView("template/footer", $local_data, $asData);
    }
}