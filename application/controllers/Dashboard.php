<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends BaseAdmin_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->loadViewAdmin("dashboard/index");
    }
}