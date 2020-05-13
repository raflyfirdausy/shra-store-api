<?php
defined('BASEPATH') or exit('No direct script access allowed');

class SuperAdmin_Controller extends BaseAdmin_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->userData->level_admin != LEVEL_SUPER_ADMIN) {
            redirect(base_url("dashboard"));
            // d("Anda tidak bisa mengakses fitur Super Admin");
        }
    }
}
