<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends SuperAdmin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Admin_model', 'admin');
        $this->load->model('Toko_model', 'toko');
    }

    public function index()
    {
        $isi['data'] = $this->admin->with_toko()->as_object()->get_all();
        $isi['data_toko'] = $this->toko->as_object()->get_all();
        $this->loadViewAdmin("admin/index", $isi);
    }

    public function tambah_admin()
    {
        $isi['data_toko'] = $this->toko->as_object()->get_all();
        $this->loadViewAdmin("admin/tambah_admin", $isi);
    }

    public function edit_admin($key = null)
    {
        $getData = $this->admin->get($key);
        if (!$getData) { // Kalo datane ga ada, antisipasi inject URL
            redirect(base_url("admin"));
        }

        $isi['data']      = $getData;
        $isi['data_toko'] = $this->toko->as_object()->get_all();

        $this->loadViewAdmin("admin/edit_admin", $isi);
    }

    public function proses_simpan_admin()
    {
        $level = $this->userData->level_admin;

        $data['nama_admin']            = $this->input->post('nama_admin');
        $data['username_admin']        = $this->input->post('username_admin');
        $data['password_admin']        = md5($this->input->post('password_admin'));
        $data['level_admin']           = $this->input->post('level_admin');
        $data['id_toko']               = empty($this->input->post('id_toko')) ? null : $this->input->post('id_toko');

        $insert = $this->admin->insert($data);
        if ($insert) { // Jika berhasil
            $this->session->set_flashdata('sukses', $data['nama_admin'] . " Berhasil di tambahkan");
        } else { // jika gagal
            $this->session->set_flashdata('gagal', 'Data gagal di tambahkan');
        }
        redirect('admin');
    }

    public function proses_update_admin()
    {
        $id_admin = $this->input->post("id_admin");
        $pass = "";

        if ($this->input->post('password_admin_baru') != "") {
            $pass  = md5($this->input->post('password_admin_baru'));
        } else {
            $pass  = $this->input->post('password_admin');
        }

        $dataUpdate     = [
            "id_toko"        => $this->input->post('id_toko'),
            "nama_admin"     => $this->input->post('nama_admin'),
            "username_admin" => $this->input->post('username_admin'),
            "password_admin" => $pass,
            "level_admin"    => $this->input->post('level_admin'),
        ];
        $update = $this->admin->update($dataUpdate, $id_admin);
        if ($update) {
            $this->session->set_flashdata('sukses', "Data " . $dataUpdate['nama_admin'] . " berhasil di update");
        } else {
            $this->session->set_flashdata('gagal', "Data gagal di update");
        }
        redirect('admin');
    }

    public function delete_admin($key = null)
    {
        $delete = $this->admin->delete($key);
        if ($delete) {
            $this->session->set_flashdata('sukses', 'Data Admin berhasil dihapus!');
        } else {
            $this->session->set_flashdata('gagal', 'Data Admin gagal dihapus!');
        }
        redirect('admin');
    }

    public function upload_dataadmin()
    {
        // Load plugin PHPExcel nya
        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';

        $config['upload_path'] = realpath('upload');
        $config['allowed_types'] = 'xlsx|xls|csv';
        $config['max_size'] = '10000';
        $config['encrypt_name'] = true;

        // $this->load->library('upload', $config);
        $this->upload->initialize($config);
        if (!$this->upload->do_upload()) {

            //upload gagal
            $this->session->set_flashdata('gagal', 'Import data gagal!' . $this->upload->display_errors() . '');
            //redirect halaman
            redirect('admin');
        } else {

            $data_upload = $this->upload->data();

            $excelreader     = new PHPExcel_Reader_Excel2007();
            $loadexcel         = $excelreader->load('upload/' . $data_upload['file_name']); // Load file yang telah diupload ke folder excel
            $sheet             = $loadexcel->getActiveSheet()->toArray(null, true, true, true);

            $data = array();

            $numrow = 1;
            foreach ($sheet as $row) {
                if ($numrow > 1) {
                    array_push($data, array(
                        'id_admin'          => $row['A'],
                        'nama_admin'        => $row['B'],
                        'username_admin'    => $row['C'],
                    ));
                }
                $numrow++;
            }
            $this->db->insert_batch('admin', $data);
            //delete file from server
            unlink(realpath('upload/' . $data_upload['file_name']));

            //upload success
            $this->session->set_flashdata('sukses', 'Import data berhasil!');
            //redirect halaman
            redirect('admin');
        }
    }
}
