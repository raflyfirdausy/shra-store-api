<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Banner extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Banner_model', 'banner');
    }

    public function index()
    {
        $isi['data'] = $this->banner->as_object()->get_all();
        $this->loadViewAdmin("banner/index", $isi);
    }

    public function tambah_banner()
    {
        $this->loadViewAdmin("banner/tambah_banner");
    }

    public function edit_banner($key = null)
    {
        $getData = $this->banner->get($key);
        if (!$getData) { // Kalo datane ga ada, antisipasi inject URL
            redirect(base_url("banner"));
        }
        $isi['data'] = $getData;
        $this->loadViewAdmin("banner/edit_banner", $isi);
    }

    public function proses_simpan_banner()
    {
        // Upload foto
        if ($_FILES['foto_banner']['name'] != "") {
            $namafile = $this->userData->id_admin . "" . time(); //? RENAME NAMA FILE PAKE UNIXTIMESTAMP
            $config  = [
                "upload_path"       => realpath('assets/banner'),
                "allowed_types"     => 'gif|jpg|jpeg|png',
                "max_size"          => 2048,
                "file_ext_tolower"  => FALSE,
                "overwrite"         => TRUE,
                "remove_spaces"     => TRUE,
                "file_name"         => $namafile
            ];

            // $config['max_size']             = 2000;
            // $config['max_width']            = 400;
            // $config['max_height']           = 400;
            $this->upload->initialize($config);

            if (!$this->upload->do_upload('foto_banner')) {
                $this->session->set_flashdata('gagal', $this->upload->display_errors());
            } else {
                $upload_data = $this->upload->data();
                $image_name = $upload_data['file_name'];
            }
        }
        $data['foto_banner']          = $image_name;
        $data['status_banner']        = $this->input->post('status_banner');

        $insert = $this->banner->insert($data);
        if ($insert) { // Jika berhasil
            $this->session->set_flashdata('sukses', "Banner baru berhasil di tambahkan");
        } else { // jika gagal
            $this->session->set_flashdata('gagal', 'Banner baru gagal di tambahkan');
        }
        redirect('banner');
    }

    public function proses_update_banner()
    {
        // Upload foto
        if ($_FILES['foto_banner']['name'] != "") {
            $namafile = $this->userData->id_toko . "" . time(); //? RENAME NAMA FILE PAKE UNIXTIMESTAMP
            $config  = [
                "upload_path"       => realpath('assets/banner'),
                "allowed_types"     => 'gif|jpg|jpeg|png',
                "max_size"          => 2048,
                "file_ext_tolower"  => FALSE,
                "overwrite"         => TRUE,
                "remove_spaces"     => TRUE,
                "file_name"         => $namafile
            ];

            // $config['max_size']             = 2000;
            // $config['max_width']            = 400;
            // $config['max_height']           = 400;
            $this->upload->initialize($config);

            if (!$this->upload->do_upload('foto_banner')) {
                $this->session->set_flashdata('gagal', $this->upload->display_errors());
            } else {
                $old = $this->input->post('foto_lama');
                if ($_FILES['foto_banner']['name'] != $old) {
                    $upload_data = $this->upload->data();
                    $image_name = $upload_data['file_name'];
                    unlink(realpath('assets/banner/' . $old));
                } else {
                    $image_name = $this->input->post('foto_lama');
                }
            }
        } else {
            $image_name = $this->input->post('foto_lama');
        }
        $id = $this->input->post("id_banner");
        $data['foto_banner']          = $image_name;
        $data['status_banner']        = $this->input->post('status_banner');

        $update = $this->banner->update($data, $id);
        if ($update) { // Jika berhasil
            $this->session->set_flashdata('sukses', "Banner berhasil diupdate");
        } else { // jika gagal
            $this->session->set_flashdata('gagal', 'Banner gagal diupdate');
        }
        redirect('banner');
    }

    public function delete_banner($key = null)
    {
        $delete = $this->banner->delete($key);
        if ($delete) {
            $this->session->set_flashdata('sukses', 'Banner berhasil dihapus!');
        } else {
            $this->session->set_flashdata('gagal', 'Banner gagal dihapus!');
        }
        redirect('banner');
    }
}
