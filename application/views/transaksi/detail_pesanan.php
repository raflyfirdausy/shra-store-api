<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Detail Pesanan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url() ?>dashboard">Home</a></li>
                        <?php if ($this->uri->segment(2) == "detail_pesanan") : ?>
                            <li class="breadcrumb-item"><a href="<?= base_url('transaksi/pesanan_masuk') ?>">Pesanan Masuk</a></li>
                        <?php else : ?>
                            <li class="breadcrumb-item">Pesanan</li>
                        <?php endif ?>
                        <li class="breadcrumb-item">Detail Pesanan</li>
                        <?php if ($this->uri->segment(2) == "pesanan_masuk") : ?>
                            <li class="breadcrumb-item active">Pesanan Masuk</li>
                        <?php elseif ($this->uri->segment(2) == "pesanan_diproses") : ?>
                            <li class="breadcrumb-item active">Pesanan Diproses</li>
                        <?php elseif ($this->uri->segment(2) == "pesanan_selesai") : ?>
                            <li class="breadcrumb-item active">Pesanan Selesai</li>
                        <?php elseif ($this->uri->segment(2) == "pesanan_dibatalkan") : ?>
                            <li class="breadcrumb-item active">Pesanan Dibatalkan</li>
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
                    <div class="card-header">
                        <a href="<?= base_url() ?>transaksi/pesanan_masuk"><i class="fa fa-arrow-left"></i> Kembali</a>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="info-box bg-light">
                            <div class="info-box-content">
                                <div class="row">
                                    <div class="col-md-4">
                                        <span class="info-box-text text-muted">Kode Transaksi</span>
                                        <span class="info-box-number text-muted mb-0"><?= $data->kode_transaksi ?></span>

                                        <span class="info-box-text text-muted">Waktu Pemesanan</span>
                                        <span class="info-box-number text-muted mb-0"><?= $data->created_at ?></span>
                                    </div>

                                    <div class="col-md-4">
                                        <span class="info-box-text text-muted">Nama Pemesan</span>
                                        <span class="info-box-number text-muted mb-0"><?= $data->user->nama_user ?></span>

                                        <span class="info-box-text text-muted">No HP</span>
                                        <span class="info-box-number text-muted mb-0"><?= $data->nohp_transaksi ?></span>


                                    </div>
                                    <div class="col-md-4">
                                        <span class="info-box-text text-muted">Alamat Pengiriman</span>
                                        <span class="info-box-number text-muted mb-0"><?= $data->alamatdetail_transaksi . ", " . $data->alamat_transaksi ?></span>

                                        <span class="info-box-text text-muted">Status Pemesanan</span>
                                        <span class="info-box-number text-muted mb-0">
                                            <?php if ($data->status_transaksi == BELUM_DIPROSES) : ?>
                                                Belum dikonfirmasi
                                            <?php elseif ($data->status_transaksi == SEDANG_DIPROSES) : ?>
                                                Belum diproses (kemas dan kirim)
                                            <?php elseif ($data->status_transaksi == SELESAI_TERKIRIM) : ?>
                                                Selesai / Sudah dikirim
                                            <?php elseif ($data->status_transaksi == DIBATALKAN) : ?>
                                                Dibatalkan
                                            <?php endif ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        Detail Pesanan
                        <div class="table-responsive">
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
                                    $detail_data = $data->detail_transaksi;
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
                        <div class="row">
                            <div class="col-md-4">
                                <label for="ongkir">Ongkos Kirim</label>
                                <input id="ongkir" class="form-control" type="text" value="<?= $data->ongkir_transaksi ?>" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="total_harga">Total Harga</label>
                                <input id="total_harga" class="form-control" type="text" value="<?= $sum ?>" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="bayar">Total Bayar</label>
                                <input id="bayar" class="form-control" type="text" value="<?= $sum + $data->ongkir_transaksi ?>" readonly>
                            </div>
                        </div>
                        <form id="formProses" method="POST" action="<?= base_url() ?>transaksi/proses_pesanan">
                        </form>
                        <form id="formTolak" method="POST" action="<?= base_url() ?>transaksi/tolak_pesanan">
                        </form>
                        <input type="hidden" form="formTolak" name="id_transaksi" value="<?= $data->id_transaksi ?>">
                        <input type="hidden" form="formProses" name="id_transaksi" value="<?= $data->id_transaksi ?>">
                    </div>
                    <div class="card-footer text-right">
                        <?php if ($data->status_transaksi == BELUM_DIPROSES) : ?>
                            <button type="submit" form="formTolak" class="btn btn-danger mr-2">Tolak Pesanan</button>

                            <input type="hidden" name="id_transaksi" value="<?= $data->id_transaksi ?>">
                            <button type="submit" form="formProses" class="btn btn-primary">Proses Pesanan</button>


                        <?php elseif ($data->status_transaksi == SEDANG_DIPROSES) : ?>

                            <button type="submit" form="formProses" class="btn btn-primary">Pesanan Telah Terkirim</button>

                        <?php else : ?>
                            <a href="<?= base_url() ?>transaksi/pesanan_masuk" class="btn btn-primary">Kembali</a>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>