<footer class="main-footer">
    <strong>Copyright &copy; 2020 <a href="<?= base_url() ?>dashboard">Doomu</a>.</strong>
    All rights reserved.
    <!-- <div class="float-right d-none d-sm-inline-block">
        <b>Version</b>
      </div> -->
</footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<script src="<?= base_url(); ?>assets/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap -->
<script src="<?= base_url(); ?>assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- DataTables -->
<script src="<?= base_url() ?>assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?= base_url() ?>assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="<?= base_url() ?>assets/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<!-- Bootstrap4 Duallistbox -->
<script src="<?= base_url() ?>assets/plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js"></script>

<script src="<?= base_url() ?>assets/plugins/toastr/toastr.min.js"></script>
<!-- SweetAlert2 -->
<script src="<?= base_url() ?>assets/plugins/sweetalert2/sweetalert2.min.js"></script>
<!-- overlayScrollbars -->
<script src="<?= base_url(); ?>assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="<?= base_url(); ?>assets/js/adminlte.js"></script>

<!-- OPTIONAL SCRIPTS -->
<script src="<?= base_url(); ?>assets/js/demo.js"></script>
<script src="<?= base_url(); ?>assets/js/custom.js"></script>

<?php if ($this->uri->segment(1) == "dashboard") : ?>
    <!-- PAGE PLUGINS -->
    <!-- jQuery Mapael -->
    <script src="<?= base_url(); ?>assets/plugins/jquery-mousewheel/jquery.mousewheel.js"></script>
    <script src="<?= base_url(); ?>assets/plugins/raphael/raphael.min.js"></script>
    <script src="<?= base_url(); ?>assets/plugins/jquery-mapael/jquery.mapael.min.js"></script>
    <script src="<?= base_url(); ?>assets/plugins/jquery-mapael/maps/usa_states.min.js"></script>
    <!-- ChartJS -->
    <script src="<?= base_url(); ?>assets/plugins/chart.js/Chart.min.js"></script>
    <!-- PAGE SCRIPTS -->
    <script src="<?= base_url(); ?>assets/js/pages/dashboard2.js"></script>
<?php endif ?>

<!-- page script -->
<script>
    $(function() {
        const Toast = Swal.mixin({
            toast: true,
            position: 'bottom-end',
            showConfirmButton: false,
            timer: 5000
        });

        <?php if ($this->session->flashdata("sukses")) : ?>
            // toastr.success('<?= $this->session->flashdata("sukses") ?>')
            Toast.fire({
                title: 'Berhasil : ',
                type: 'success',
                text: "<?= $this->session->flashdata("sukses") ?>",
            })
        <?php elseif ($this->session->flashdata("gagal")) : ?>
            // toastr.error('<?= $this->session->flashdata("gagal") ?>')
            Toast.fire({
                title: 'Gagal : ',
                type: 'error',
                text: "<?= $this->session->flashdata("gagal") ?>",
            })
        <?php endif ?>
    });
</script>

<?php
if ($this->userData->level_admin == LEVEL_ADMIN) : ?>
    <audio id="myAudio">
        <source src="<?= asset("ringtone/bell.mp3") ?>" type="audio/mpeg">
    </audio>
    <script>
        function cekPesanan() {
            var bell = document.getElementById("myAudio");
            $.ajax({
                url: "<?= base_url('transaksi/cekPesanan') ?>",
                type: "GET",
                data: null,
                dataType: "JSON",
                contentType: false,
                cache: false,
                processData: false,
                success: function(e) {
                    console.log(e);
                    if (e.status) {
                        bell.play();
                        if ("<?= $this->router->fetch_class()  ?>" != "transaksi") {
                            $(function() {
                                const Toast = Swal.mixin({
                                    toast: true,
                                    position: 'bottom-start',
                                    customClass: {
                                        confirmButton: 'btn btn-success',
                                    },
                                    buttonsStyling: false
                                });
                                Toast.fire({
                                    title: 'Ada Pesanan Masuk',
                                    // text: "Cek Sekarang",
                                    type: 'info',
                                    confirmButtonText: 'Lihat',
                                }).then((result) => {
                                    if (result.value) {
                                        window.location.href = "<?= base_url('transaksi/pesanan_masuk') ?>";
                                    }
                                });
                            });
                            // window.location.href = "<?= base_url('transaksi/pesanan_masuk') ?>";
                        } else {
                            if ("<?= $this->router->fetch_class() . '/' . $this->router->fetch_method(); ?>" == "transaksi/pesanan_masuk") {
                                // window.location.href = "<?= base_url('transaksi/pesanan_masuk') ?>";
                                $("#tabel_pesanan").load(window.location.href + " #tabel_pesanan");
                            }
                        }
                    } else {
                        if ("<?= $this->router->fetch_class() . '/' . $this->router->fetch_method(); ?>" == "transaksi/pesanan_masuk") {
                            window.location.href = "<?= base_url('transaksi/pesanan_masuk') ?>";
                        }
                    }
                },
                error: function(xhr, ajaxOptions, thrownError) { // Ketika ada error
                    console.log(xhr.responseText);
                }
            });
        }

        $(document).ready(function() {
            setInterval(function() {
                cekPesanan();
            }, 5000);
        });
    </script>
<?php endif ?>

</body>

</html>