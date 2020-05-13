<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin_Controller extends BaseAdmin_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->userData->level_admin != LEVEL_ADMIN) {
            // redirect(base_url("superadmin"));
            // d("Anda tidak bisa mengakses fitur Admin");
        }
    }
}
