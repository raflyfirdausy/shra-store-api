<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?= base_url() ?>dashboard" class="brand-link mx-0">
        <img src="<?= base_url() ?>assets/img/doomuLogo_white.png" alt="Doomu Logo" class="brand-image elevation-3" style="opacity: .8">
        <!-- <span class="brand-text font-weight-light">Kopkun Sale</span> -->
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <!-- <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">Alexander Pierce</a>
            </div>
        </div> -->

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent nav-compact" data-widget="treeview" role="menu" data-accordion="false">

                <?php if ($this->userData->level_admin == LEVEL_SUPER_ADMIN) : ?>
                    <!-- Menu Superadmin -->
                    <li class="nav-header">MASTER DATA SUPERADMIN</li>
                    <li class="nav-item">
                        <a href="<?= base_url() ?>dashboard" class="nav-link
                        <?= ($this->uri->segment(1) == "dashboard") ? "active" : ""; ?>">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>
                                Dashboard
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url() ?>barang" class="nav-link
                    <?= ($this->uri->segment(1) == "barang") ? "active" : ""; ?>">
                            <i class="nav-icon fas fa-th"></i>
                            <p>
                                Master Data Barang
                                <!-- <span class="right badge badge-danger">New</span> -->
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url() ?>admin" class="nav-link
                        <?= ($this->uri->segment(1) == "admin") ? "active" : ""; ?>">
                            <i class="nav-icon fas fa-copy"></i>
                            <p>
                                Master Data Admin
                                <!-- <i class="fas fa-angle-left right"></i> -->
                                <!-- <span class="badge badge-info right">6</span> -->
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url() ?>toko" class="nav-link
                    <?= ($this->uri->segment(1) == "toko") ? "active" : ""; ?>">
                            <i class="nav-icon fas fa-chart-pie"></i>
                            <p>
                                Master Data Toko
                            </p>
                        </a>
                    </li>
                <?php endif ?>

                <?php if ($this->userData->level_admin == LEVEL_ADMIN) : ?>
                    <!-- Menu Admin -->
                    <li class="nav-header">MASTER DATA ADMIN</li>
                    <li class="nav-item">
                        <a href="<?= base_url() ?>dashboard" class="nav-link
                        <?= ($this->uri->segment(1) == "dashboard") ? "active" : ""; ?>">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>
                                Dashboard
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url() ?>barang" class="nav-link
                    <?= ($this->uri->segment(1) == "barang") ? "active" : ""; ?>">
                            <i class="nav-icon fas fa-th"></i>
                            <p>
                                Data Barang
                                <!-- <span class="right badge badge-danger">New</span> -->
                            </p>
                        </a>
                    </li>
                    <li class="nav-item has-treeview <?= ($this->uri->segment(1) == "transaksi" && $this->uri->segment(2) != "history") ? "menu-open" : "" ?> ">
                        <a href="#" class="nav-link <?= ($this->uri->segment(1) == "transaksi" && $this->uri->segment(2) != "history") ? "active" : "" ?> ">
                            <i class="nav-icon fas fa-file-alt"></i>
                            <p>Transaksi
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= base_url() ?>transaksi/pesanan_masuk" class="nav-link <?= ($this->uri->segment(2) == "pesanan_masuk" || $this->uri->segment(2) == "detail_pesanan") ? "active" : "" ?>">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Pesanan Masuk</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url() ?>transaksi/pesanan_diproses" class="nav-link <?= ($this->uri->segment(2) == "pesanan_diproses") ? "active" : "" ?>">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Pesanan Diproses</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url() ?>transaksi/pesanan_dibatalkan" class="nav-link <?= ($this->uri->segment(2) == "pesanan_dibatalkan") ? "active" : "" ?>">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Pesanan Dibatalkan</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url() ?>transaksi/pesanan_selesai" class="nav-link <?= ($this->uri->segment(2) == "pesanan_selesai") ? "active" : "" ?>">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Pesanan Selesai</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url() ?>transaksi/history" class="nav-link <?= ($this->uri->segment(2) == "history") ? "active" : "" ?>">
                            <i class="nav-icon fas fa-history"></i>
                            <p>Riwayat Transaksi</p>
                        </a>
                    </li>
                    <!-- <li class="nav-header">DATA TOKO</li> -->
                    <li class="nav-item">
                        <a href="<?= base_url() ?>toko/data_toko" class="nav-link <?= ($this->uri->segment(2) == "data_toko") ? "active" : "" ?>">
                            <i class="nav-icon fas fa-cog"></i>
                            <p>Pengaturan Data Toko</p>
                        </a>
                    </li>
                <?php endif ?>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>