<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Toko extends BaseAdmin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Toko_model', 'toko');
        $this->load->model('Admin_model', 'admin');
    }

    public function index()
    {
        $isi['data'] = $this->toko->as_object()->get_all();
        $this->loadViewAdmin("toko/index", $isi);
    }

    public function tambah_toko()
    {
        $this->loadViewAdmin("toko/tambah_toko");
    }

    public function proses_simpan_toko()
    {
        $data['nama_toko']            = $this->input->post('nama_toko');
        $data['alamat_toko']          = $this->input->post('alamat_toko');
        $data['no_toko']              = $this->input->post('no_toko');
        $data['min_km_toko']          = $this->input->post('min_km_toko');
        $data['ongkir_toko']          = $this->input->post('ongkir_toko');
        $data['ongkirtambahan_toko']  = $this->input->post('ongkirtambahan_toko');
        $data['latitude_toko']        = $this->input->post('latitude_toko');
        $data['longitude_toko']       = $this->input->post('longitude_toko');
        
        $insert = $this->toko->insert($data);
        if ($insert) { // Jika berhasil
            $this->session->set_flashdata('sukses', $data['nama_mastertoko'] . " Berhasil di tambahkan");
        } else { // jika gagal
            $this->session->set_flashdata('gagal', 'Data gagal di tambahkan');
        }
        redirect('toko');
    }

    public function edit_toko($key = null)
    {
        $getData = $this->toko->get($key);
        if (!$getData) { // Kalo datane ga ada, antisipasi inject URL
            redirect(base_url("toko"));
        }
        $isi['data'] = $getData;
        $this->loadViewAdmin("toko/edit_toko", $isi);
    }
    
    public function proses_update_toko()
    {
        $level = $this->userData->level_admin;
        $id_toko = $this->input->post("id_toko");
        $dataUpdate     = [
            "nama_toko"             => $this->input->post('nama_toko'),
            "alamat_toko"           => $this->input->post('alamat_toko'),
            "min_km_toko"           => $this->input->post('min_km_toko'),
            "no_toko"               => $this->input->post('no_toko'),
            "ongkir_toko"           => $this->input->post('ongkir_toko'),
            "ongkirtambahan_toko"   => $this->input->post('ongkirtambahan_toko'),
            "latitude_toko"         => $this->input->post('latitude_toko'),
            "longitude_toko"        => $this->input->post('longitude_toko'),
        ];
        $update = $this->toko->update($dataUpdate, $id_toko);
        if ($update) {
            $this->session->set_flashdata('sukses', "Data " . $dataUpdate['nama_toko'] . " berhasil di update");
        } else {
            $this->session->set_flashdata('gagal', "Data " . $dataUpdate['nama_toko'] . " gagal di update");
        }
        if ($level == LEVEL_SUPER_ADMIN) {
            redirect('toko');
        } elseif ($level == LEVEL_ADMIN) {
            redirect('toko/data_toko');
        }
    }

    public function delete_toko($key = null)
    {
        $delete = $this->toko->delete($key);
        if ($delete) {
            $this->session->set_flashdata('sukses', 'Data berhasil dihapus!');
        } else {
            $this->session->set_flashdata('gagal', 'Data gagal dihapus!');
        }
        redirect('toko');
    }

    public function upload_datatoko()
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
            redirect('toko');
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
                        'nama_toko'               => $row['B'],
                        'alamat_toko'             => $row['C'],
                        'no_toko'                 => $row['D'],
                    ));
                }
                $numrow++;
            }
            $this->db->insert_batch('master_toko', $data);
            //delete file from server
            unlink(realpath('upload/' . $data_upload['file_name']));

            //upload success
            $this->session->set_flashdata('sukses', 'Import data berhasil!');
            //redirect halaman
            redirect('toko');
        }
    }

    public function data_toko($key = null)
    {
        $key = $this->userData->id_toko;
        $getData = $this->toko->get($key);
        if (!$getData) { // Kalo datane ga ada, antisipasi inject URL
            redirect(base_url("toko"));
        }
        $isi['data'] = $getData;
        $this->loadViewAdmin("toko/data_toko", $isi);
    }
}
