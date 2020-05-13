<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <?php if ($this->uri->segment(2) == "pesanan_masuk") : ?>
                        <h1>Transaksi Masuk</h1>
                    <?php elseif ($this->uri->segment(2) == "pesanan_diproses") : ?>
                        <h1>Transaksi Diproses</h1>
                    <?php elseif ($this->uri->segment(2) == "pesanan_selesai") : ?>
                        <h1>Transaksi Selesai</h1>
                    <?php elseif ($this->uri->segment(2) == "pesanan_dibatalkan") : ?>
                        <h1>Transaksi Dibatalkan</h1>
                    <?php endif ?>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url() ?>dashboard">Home</a></li>
                        <li class="breadcrumb-item">Transaksi</li>
                        <?php if ($this->uri->segment(2) == "pesanan_masuk") : ?>
                            <li class="breadcrumb-item active">Transaksi Masuk</li>
                        <?php elseif ($this->uri->segment(2) == "pesanan_diproses") : ?>
                            <li class="breadcrumb-item active">Transaksi Diproses</li>
                        <?php elseif ($this->uri->segment(2) == "pesanan_selesai") : ?>
                            <li class="breadcrumb-item active">Transaksi Selesai</li>
                        <?php elseif ($this->uri->segment(2) == "pesanan_dibatalkan") : ?>
                            <li class="breadcrumb-item active">Transaksi Dibatalkan</li>
                        <?php endif ?>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- asdad -->
    <!-- Main content -->
    <section class="content">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <!-- <div class="card-header">
                        
                    </div> -->
                    <!-- /.card-header -->
                    <div class="card-body table-responsive">
                        <table id="tabel_pesanan" class="table table-bordered table-hover display responsive nowrap" width="100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Kode Transaksi</th>
                                    <th>Pemesan</th>
                                    <th>Alamat Transaksi</th>
                                    <th>No HP</th>
                                    <th>Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($data) :
                                    $no = 1;
                                    foreach ($data as $row) :
                                ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= $row->kode_transaksi ?></td>
                                            <td><?= $row->user->nama_user ?></td>
                                            <td><?= $row->alamatdetail_transaksi . ", " . $row->alamat_transaksi ?></td>
                                            <td><?= $row->nohp_transaksi ?></td>
                                            <td>
                                                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#detailPesan-<?= $row->id_transaksi ?>"><i class="fa fa-eye text-sm"></i>
                                                </button>
                                                <!-- <a href="<?= base_url() ?>transaksi/detail_pesanan" class="btn btn-info"><i class="fa fa-eye"></i></a> -->

                                                <!-- <a href="<?= base_url() ?>toko/edit_toko/<?= $row->id_toko ?>" class="btn btn-primary"><i class="fa fa-pencil-alt"></i></a>
                                                <a href="<?= base_url() ?>toko/delete_toko/<?= $row->id_toko ?>" class="btn btn-danger" onclick="return confirm('Hapus data toko ini?');"><i class="fa fa-trash"></i></a> -->
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                <?php endif ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php
if ($data) :
    $no = 1;
    foreach ($data as $row) : ?>
        <!-- Modal Detail Pesanan -->
        <div class="modal fade" id="detailPesan-<?= $row->id_transaksi ?>">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">Data Pesanan</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <form id="formProses" method="POST" action="<?= base_url() ?>transaksi/proses_pesanan">
                            <div class="info-box bg-light">
                                <div class="info-box-content">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <span class="info-box-text text-muted">Kode Transaksi</span>
                                            <span class="info-box-number text-muted mb-0"><?= $row->kode_transaksi ?></span>

                                            <span class="info-box-text text-muted">Waktu Pemesanan</span>
                                            <span class="info-box-number text-muted mb-0"><?= $row->created_at ?></span>
                                        </div>

                                        <div class="col-md-4">
                                            <span class="info-box-text text-muted">Nama Pemesan</span>
                                            <span class="info-box-number text-muted mb-0"><?= $row->user->nama_user ?></span>

                                            <span class="info-box-text text-muted">No HP</span>
                                            <span class="info-box-number text-muted mb-0"><?= $row->nohp_transaksi ?></span>


                                        </div>
                                        <div class="col-md-4">
                                            <span class="info-box-text text-muted">Alamat Pengiriman</span>
                                            <span class="info-box-number text-muted mb-0"><?= $row->alamatdetail_transaksi . ", " . $row->alamat_transaksi ?></span>

                                            <span class="info-box-text text-muted">Status Pemesanan</span>
                                            <span class="info-box-number text-muted mb-0">
                                                <?php if ($row->status_transaksi == BELUM_DIPROSES) : ?>
                                                    Belum dikonfirmasi
                                                <?php elseif ($row->status_transaksi == SEDANG_DIPROSES) : ?>
                                                    Belum diproses (kemas dan kirim)
                                                <?php elseif ($row->status_transaksi == SELESAI_TERKIRIM) : ?>
                                                    Selesai / Sudah dikirim
                                                <?php elseif ($row->status_transaksi == DIBATALKAN) : ?>
                                                    Dibatalkan
                                                <?php endif ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <label for="">Detail Pesanan</label>
                                    <table id="" class="table table-bordered table-hover table-head-fixed">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Nama</th>
                                                <th>Harga Satuan</th>
                                                <th>Qty</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $detail_data = $row->detail_transaksi;
                                            if ($detail_data) :
                                                $no = 1;
                                                $sum = 0;
                                                $rpTotal = 0;
                                                // d($detail_data);
                                                foreach ($detail_data as $detail) :
                                                    if (isset($detail->hargadiskon_barang) == null) :
                                                        $rpTotal = $detail->hargajual_barang * $detail->banyak_barang;
                                                        $sum += $detail->hargajual_barang * $detail->banyak_barang;
                                                    else :
                                                        $rpTotal = $detail->hargadiskon_barang * $detail->banyak_barang;
                                                        $sum += $detail->hargadiskon_barang * $detail->banyak_barang;
                                                    endif;
                                            ?>
                                                    <tr>
                                                        <td><?= $no++; ?></td>
                                                        <td><?= $detail->barang->nama_barang ?></td>
                                                        <td>
                                                            <?php if (isset($detail->hargadiskon_barang)) : ?>
                                                                <del><?= $detail->hargajual_barang ?></del><?= $detail->hargadiskon_barang ?>
                                                            <?php else : ?>
                                                                <?= $detail->hargajual_barang ?>
                                                            <?php endif ?>

                                                        </td>
                                                        <td><?= $detail->banyak_barang ?> item</td>
                                                        <td><?= $rpTotal ?></td>
                                                    </tr>
                                                <?php endforeach ?>
                                            <?php endif ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="ongkir">Ongkos Kirim</label>
                                    <input id="ongkir" class="form-control" type="text" value="<?= $row->ongkir_transaksi ?>" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label for="bayar">Total Harga</label>
                                    <input id="bayar" class="form-control" type="text" value="<?= $sum ?>" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label for="bayar">Total Bayar</label>
                                    <input id="bayar" class="form-control" type="text" value="<?= $sum + $row->ongkir_transaksi ?>" readonly>
                                </div>
                            </div>
                    </div>
                    <input type="hidden" name="id_transaksi" value="<?= $row->id_transaksi ?>">


                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <!-- <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button> -->
                        <?php if ($row->status_transaksi == BELUM_DIPROSES) : ?>
                            <form id="formTolak" method="POST" action="<?= base_url() ?>transaksi/tolak_pesanan">
                                <input type="hidden" name="id_transaksi" value="<?= $row->id_transaksi ?>">
                                <button type="submit" form="formTolak" class="btn btn-danger">Tolak Pesanan</button>
                            </form>

                            <button type="submit" form="formProses" class="btn btn-primary">Proses Pesanan</button>

                        <?php elseif ($row->status_transaksi == SEDANG_DIPROSES) : ?>
                            <button type="submit" form="formProses" class="btn btn-primary">Pesanan Telah Terkirim</button>
                        <?php else : ?>
                            <button type="button" form="formProses" class="btn btn-primary" data-dismiss="modal">Selesai</button>
                        <?php endif ?>
                    </div>
                    </form>
                </div>
            </div>
        </div>

    <?php endforeach ?>
<?php endif ?>