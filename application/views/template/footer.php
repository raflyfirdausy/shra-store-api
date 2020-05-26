<footer class="main-footer">
    <strong>Copyright &copy; <?= ((date('Y') - 2020) == 0) ? "2020" : "2020-" . date('Y'); ?> <a href="<?= base_url() ?>dashboard">SHRA</a>.</strong>
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
<!-- InputMask -->
<script src="<?= base_url() ?>assets/plugins/moment/moment.min.js"></script>
<script src="<?= base_url() ?>assets/plugins/inputmask/min/jquery.inputmask.bundle.min.js"></script>
<!-- Toastr -->
<script src="<?= base_url() ?>assets/plugins/toastr/toastr.min.js"></script>
<!-- SweetAlert2 -->
<script src="<?= base_url() ?>assets/plugins/sweetalert2/sweetalert2.min.js"></script>
<!-- overlayScrollbars -->
<script src="<?= base_url(); ?>assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="<?= base_url(); ?>assets/js/adminlte.js"></script>

<!-- OPTIONAL SCRIPTS -->
<script src="<?= base_url(); ?>assets/js/demo.js"></script>

<!-- page script -->
<script>
    $(function() {
        $('#table').DataTable({
            "lengthMenu": [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
            "language": {
                "search": "Cari",
                "lengthMenu": "Tampilkan _MENU_",
                "zeroRecords": "Tidak Ada Data",
                "info": "Menampilkan _PAGE_ dari _PAGES_",
                "infoEmpty": "Tidak Ada Data",
                "infoFiltered": "(difilter dari sebanyak _MAX_)"
            }
        });

        $('#tabel_pesanan').DataTable({
            "lengthMenu": [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
            "language": {
                "search": "Cari",
                "lengthMenu": "Tampilkan _MENU_",
                "zeroRecords": "Tidak Ada Data",
                "info": "Menampilkan _PAGE_ dari _PAGES_",
                "infoEmpty": "Tidak Ada Data",
                "infoFiltered": "(difilter dari sebanyak _MAX_)"
            }
        });

        //Bootstrap Duallistbox
        $('.duallistbox').bootstrapDualListbox()

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

        $('[data-mask]').inputmask()

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#imgprev').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#imgInp").change(function() {
            readURL(this);
        });

        $('#imgdel').on('click', function() {
            <?php if ($this->uri->segment(1) == "banner") : ?>
                $('#imgprev').attr('src', "<?= base_url() ?>assets/banner/banner1.jpg");
            <?php else : ?>
                $('#imgprev').attr('src', "<?= base_url() ?>assets/img/no-img.jpg");
            <?php endif ?>
            $('#imgInp').val("");
        });

    });
</script>

<?php
if ($this->userData->level_admin == LEVEL_ADMIN) : ?>
    <audio id="myAudio">
        <source src="<?= asset("ringtone/order_masuk.mp3") ?>" type="audio/mpeg">
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
                        if ("<?= $this->router->fetch_class()  ?>" != "transaksi" || "<?= $this->router->fetch_class() . '/' . $this->router->fetch_method(); ?>" == "transaksi/history") {
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