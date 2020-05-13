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
    $('#imgprev').attr('src', "<?= base_url() ?>assets/img/no-img.jpg");
    $('#imgInp').val("");
});