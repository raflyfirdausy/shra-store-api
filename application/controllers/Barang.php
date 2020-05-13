<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Barang extends BaseAdmin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Master_barang_model', 'master_barang');
        $this->load->model('Barang_model', 'barang');
        $this->load->model('Kategori_barang_model', 'kategori_barang');
    }

    public function index()
    {
        $level = $this->userData->level_admin;
        $idtoko = $this->userData->id_toko;
        if ($level == LEVEL_SUPER_ADMIN) {
            $getData = $this->master_barang->with_kategori()->as_object()->get_all();
            // if (!$getData) { // Kalo datane ga ada, antisipasi inject URL
            //     redirect(base_url("barang"));
            // }
            $isi['data'] = $getData;
        } elseif ($level == LEVEL_ADMIN) {
            $getData_Barang    = $this->master_barang->as_object()->get_all();
            $getData = $this->barang->with_kategori()->where('id_toko', $idtoko)->as_object()->get_all();
            if (!$getData && !$getData_Barang) { // Kalo datane ga ada, antisipasi inject URL
                redirect(base_url("barang"));
            }
            $isi['data'] = $getData;
            $isi['data_barang'] = $getData_Barang;
        }
        $isi['level'] = $level;
        $this->loadViewAdmin("barang/index", $isi);
    }

    public function tambah_barang()
    {
        $data["kategori_barang"]    = $this->kategori_barang->as_object()->get_all();
        $this->loadViewAdmin("barang/tambah_barang", $data);
    }

    public function proses_simpan_barang()
    {
        $level = $this->userData->level_admin;

        // Upload foto
        if ($_FILES['foto_masterbarang']['name'] != "") {
            $namafile = $this->userData->id_toko . "" . time(); //? RENAME NAMA FILE PAKE UNIXTIMESTAMP
            $config  = [
                "upload_path"       => realpath('assets/barang'),
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

            if (!$this->upload->do_upload('foto_masterbarang')) {
                $this->session->set_flashdata('gagal', $this->upload->display_errors());
            } else {
                $upload_data = $this->upload->data();
                $image_name = $upload_data['file_name'];
            }
        }

        if ($level == LEVEL_SUPER_ADMIN) {
            $data['id_kategori_barang']     = empty($this->input->post('kategori_masterbarang')) ? null : $this->input->post('kategori_masterbarang');
            $data['kode_barang']            = $this->input->post('kode_barang');
            $data['nama_masterbarang']      = $this->input->post('nama_masterbarang');
            $data['hargajual_masterbarang'] = $this->input->post('hargajual_masterbarang');
            $data['foto_masterbarang']      = empty($image_name) ? null : $image_name;
            $insert = $this->master_barang->insert($data);
        } elseif ($level == LEVEL_ADMIN) {
            $data['id_toko']                = $this->userData->id_toko;
            $data['id_kategori_barang']     = empty($this->input->post('kategori_masterbarang')) ? null : $this->input->post('kategori_masterbarang');
            $data['kode_barang']            = $this->input->post('kode_barang');
            $data['nama_barang']            = $this->input->post('nama_masterbarang');
            $data['hargajual_barang']       = $this->input->post('hargajual_masterbarang');
            $data['hargadiskon_barang']     = empty($this->input->post('hargadiskon_barang')) ? null : $this->input->post('hargadiskon_barang');
            $data['foto_barang']            = empty($image_name) ? null : $image_name;
            $insert = $this->barang->insert($data);
        }

        if ($insert) { // Jika berhasil
            $this->session->set_flashdata('sukses', $data['nama_masterbarang'] . " Berhasil di tambahkan");
        } else { // jika gagal
            $this->session->set_flashdata('gagal', 'Data gagal di tambahkan');
        }
        redirect('barang');
    }

    public function edit_barang($key = null)
    {
        $level = $this->userData->level_admin;
        $idtoko = $this->userData->id_toko;

        $isi["kategori_barang"]     = $this->kategori_barang->as_object()->get_all(); // Isi Kategori

        if ($level == LEVEL_SUPER_ADMIN) { // Level Supadmin
            $getData = $this->master_barang->with_kategori()->get($key);
            if (!$getData) { // Kalo datane ga ada, antisipasi inject URL
                redirect(base_url("barang"));
            }
            $isi['data']                = $getData;
            $this->loadViewAdmin("barang/edit_barang", $isi);
        } elseif ($level == LEVEL_ADMIN) { // Level admin
            $getData = $this->barang->with_kategori()->where('id_toko', $idtoko)->get($key);
            if (!$getData) { // Kalo datane ga ada, antisipasi inject URL
                redirect(base_url("barang"));
            }
            $isi['data']                = $getData;
            $this->loadViewAdmin("barang/edit_barang_admin", $isi);
        }
    }

    public function proses_update_barang()
    {
        $level = $this->userData->level_admin;
        // Upload foto
        if ($_FILES['foto_masterbarang']['name'] != "") {
            $namafile = $this->userData->id_toko . "" . time(); //? RENAME NAMA FILE PAKE UNIXTIMESTAMP
            $config  = [
                "upload_path"       => realpath('assets/barang'),
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

            if (!$this->upload->do_upload('foto_masterbarang')) {
                $this->session->set_flashdata('gagal', $this->upload->display_errors());
            } else {
                $old = $this->input->post('foto_lama');
                if ($_FILES['foto_masterbarang']['name'] != $old) {
                    $upload_data = $this->upload->data();
                    $image_name = $upload_data['file_name'];
                    unlink(realpath('assets/barang/' . $old));
                } else {
                    $image_name = $this->input->post('foto_lama');
                }
            }
        } else {
            $image_name = $this->input->post('foto_lama');
        }

        if ($level == LEVEL_SUPER_ADMIN) {
            $id = $this->input->post("id_masterbarang");
            $dataUpdate     = [
                "kode_barang"               => $this->input->post('kode_barang'),
                "nama_masterbarang"         => $this->input->post('nama_masterbarang'),
                "hargajual_masterbarang"    => $this->input->post('hargajual_masterbarang'),
                "id_kategori_barang"        => empty($this->input->post('kategori_masterbarang')) ? null : $this->input->post('kategori_masterbarang'),
                "foto_masterbarang"         => $image_name,
            ];
            $update = $this->master_barang->update($dataUpdate, $id);
        } elseif ($level == LEVEL_ADMIN) {
            $id = $this->input->post("id_barang");
            $dataUpdate     = [
                "kode_barang"           => $this->input->post('kode_barang'),
                "nama_barang"           => $this->input->post('nama_barang'),
                "hargajual_barang"      => $this->input->post('hargajual_barang'),
                "hargadiskon_barang"    => empty($this->input->post('hargadiskon_barang')) ? null : $this->input->post('hargadiskon_barang'),
                "id_kategori_barang"    => empty($this->input->post('kategori_barang')) ? null : $this->input->post('kategori_barang'),
                "foto_barang"           => $image_name,
            ];
            $update = $this->barang->update($dataUpdate, $id);
        }

        if ($update) {
            $this->session->set_flashdata('sukses', "Data " . (empty($dataUpdate['nama_masterbarang']) ? $dataUpdate['nama_barang'] : $dataUpdate['nama_masterbarang']) . " berhasil di update");
        } else {
            $this->session->set_flashdata('gagal', "Data gagal di update");
        }
        redirect('barang');
    }

    public function delete_barang($key = null)
    {
        $level = $this->userData->level_admin;

        if ($level == LEVEL_SUPER_ADMIN) {
            $getData = $this->master_barang->with_kategori()->get($key);
            if (!$getData) { // Kalo datane ga ada, antisipasi inject URL
                redirect(base_url("barang"));
            }
            // $getData = $this->master_barang->get($key);
            $delete = $this->master_barang->delete($key);
        } elseif ($level == LEVEL_ADMIN) {
            $getData = $this->barang->with_kategori()->get($key);
            if (!$getData) { // Kalo datane ga ada, antisipasi inject URL
                redirect(base_url("barang"));
            }
            $delete = $this->barang->delete($key);
        }
        if ($delete) {
            // Hapus foto di folder
            // if ($getData['foto_masterbarang'] != "") {
            //     $tes = unlink(realpath('assets/barang/' . $getData['foto_masterbarang']));
            // }
            $this->session->set_flashdata('sukses', 'Data berhasil dihapus!');
        } else {
            $this->session->set_flashdata('gagal', 'Data gagal dihapus!');
        }
        redirect('barang');
    }

    public function upload_databarang()
    {
        $level = $this->userData->level_admin;
        if ($level != LEVEL_SUPER_ADMIN) { // Kalo bukan Level Supadmin
            redirect(base_url("barang"));
        }
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
            redirect('barang');
        } else {

            $data_upload = $this->upload->data();

            $excelreader     = new PHPExcel_Reader_Excel2007();
            $loadexcel         = $excelreader->load('upload/' . $data_upload['file_name']); // Load file yang telah diupload ke folder excel
            $sheet             = $loadexcel->getActiveSheet()->toArray(null, true, true, true);

            $data = array();

            $numrow = 1;
            $getDataMaster = $this->master_barang->get_all();

            if ($getDataMaster) {
                foreach ($getDataMaster as $data_barang) {
                    $kode_barang[] = $data_barang['kode_barang'];
                }
                foreach ($sheet as $row) {
                    if ($numrow > 1) {
                        if (!in_array($row['B'], $kode_barang)) {
                            array_push($data, array(
                                'kode_barang'               => $row['B'],
                                'nama_masterbarang'         => $row['C'],
                                'hargajual_masterbarang'    => $row['D'],
                            ));
                        } else {
                            $data = null;
                        }
                    }
                    $numrow++;
                }
            } else {
                // d($data);
                foreach ($sheet as $row) {
                    if ($numrow > 1) {
                        array_push($data, array(
                            'kode_barang'               => $row['B'],
                            'nama_masterbarang'         => $row['C'],
                            'hargajual_masterbarang'    => $row['D'],
                        ));
                    }
                    $numrow++;
                }
            }
            // d($data);
            if ($data != null) {
                $insert = $this->db->insert_batch('master_barang', $data);
                if ($insert) { //upload success
                    $this->session->set_flashdata('sukses', 'Import data berhasil!');
                } else { //upload gagal
                    $this->session->set_flashdata('gagal', 'Import data gagal!');
                }
            } else { //upload gagal
                $this->session->set_flashdata('gagal', 'Data kosong atau sudah pernah terimport!');
            }
            //delete file from server
            unlink(realpath('upload/' . $data_upload['file_name']));
            //redirect halaman
            redirect('barang');
        }
    }

    public function proses_ambil_barang($key = null)
    {
        $level = $this->userData->level_admin;
        if ($level != LEVEL_ADMIN) { // Kalo bukan Level Supadmin
            redirect(base_url("barang"));
        }

        $dataInsert = array();
        $kode_barang_toko = array();

        $key = $this->input->post('kode_barang');
        $getData = $this->master_barang->where('kode_barang', $key)->get_all();
        $getDataB = $this->barang->where('kode_barang', $key)->get_all();

        if (!empty($getDataB)) {

            foreach ($getDataB as $data_barang) {
                $kode_barang_toko[] = $data_barang['kode_barang'];
            }

            foreach ($getData as $data_master) {
                if (!in_array($data_master['kode_barang'], $kode_barang_toko)) {
                    array_push($dataInsert, array(
                        'id_toko'               =>  $this->userData->id_toko,
                        'id_kategori_barang'    =>  $data_master['id_kategori_barang'],
                        'kode_barang'           =>  $data_master['kode_barang'],
                        'nama_barang'           =>  $data_master['nama_masterbarang'],
                        'hargabeli_barang'      =>  $data_master['hargabeli_masterbarang'],
                        'hargajual_barang'      =>  $data_master['hargajual_masterbarang'],
                        'hargadiskon_barang'    =>  null,
                        'foto_barang'           =>  empty($data_master['foto_masterbarang']) ? null : $data_master['foto_masterbarang'],
                    ));
                } else {
                    $dataInsert = null;
                }
            }
        } else {
            foreach ($getData as $data_master) {
                array_push($dataInsert, array(
                    'id_toko'               =>  $this->userData->id_toko,
                    'id_kategori_barang'    =>  $data_master['id_kategori_barang'],
                    'kode_barang'           =>  $data_master['kode_barang'],
                    'nama_barang'           =>  $data_master['nama_masterbarang'],
                    'hargabeli_barang'      =>  $data_master['hargabeli_masterbarang'],
                    'hargajual_barang'      =>  $data_master['hargajual_masterbarang'],
                    'hargadiskon_barang'    =>  null,
                    'foto_barang'           =>  empty($data_master['foto_masterbarang']) ? null : $data_master['foto_masterbarang'],
                ));
            }
        }

        if ($dataInsert != null) {
            $insert = $this->db->insert_batch('barang', $dataInsert);

            if ($insert) { // Jika berhasil
                $this->session->set_flashdata('sukses', "Data-data terpilih Berhasil di tambahkan");
            } else { // jika gagal
                $this->session->set_flashdata('gagal', 'Data-data terpilih gagal di tambahkan');
            }
        } else { // jika gagal
            $this->session->set_flashdata('gagal', 'Data kosong atau sudah pernah ditambahkan');
        }
        redirect('barang');
    }
}
