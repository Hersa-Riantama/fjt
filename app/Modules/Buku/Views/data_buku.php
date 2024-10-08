<?= $this->extend('template/admin_template'); ?>
<?= $this->section('content'); ?>
<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">Buku /</span> List Buku</h4>
        <div class="row justify-content-end">
            <!-- <div class="col-xl-8">
            </div> -->
            <div class="col-xl-auto mb-4 justify-conten-end">
                <button class="btn btn-primary d-grid">Tambah Buku</button>
            </div>
        </div>
        <!-- Basic Bootstrap Table -->
        <div class="card">
            <h5 class="card-header">List Buku</h5>
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Kode Buku</th>
                            <th>Judul Buku</th>
                            <th>Pengarang</th>
                            <th>Target Terbit</th>
                            <th>Warna</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0" id="bukuData">
                    </tbody>
                </table>
            </div>
        </div>
        <!--/ Basic Bootstrap Table -->
        <!-- Edit Modal -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="editForm" action="javascript:void(0);" method="post">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel">Edit Buku</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="kode_buku" class="form-label">Kode Buku</label>
                                <input type="text" class="form-control" id="kode_buku" name="kode_buku" required>
                            </div>
                            <div class="mb-3">
                                <label for="judul_buku" class="form-label">Judul Buku</label>
                                <input type="text" class="form-control" id="judul_buku" name="judul_buku" required>
                            </div>
                            <div class="mb-3">
                                <label for="pengarang" class="form-label">Pengarang</label>
                                <input type="text" class="form-control" id="pengarang" name="pengarang" required>
                            </div>
                            <div class="mb-3">
                                <label for="target_terbit" class="form-label">Target Terbit</label>
                                <input type="year" class="form-control" id="target_terbit" name="target_terbit" required>
                            </div>
                            <div class="mb-3">
                                <label for="warna" class="form-label">Warna</label>
                                <input type="text" class="form-control" id="warna" name="warna" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" id="btn-update" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


    </div>
    <!-- / Content -->

    <!-- Footer -->
    <footer class="content-footer footer bg-footer-theme">
        <div class="container-xxl d-flex flex-wrap justify-content-center py-2 flex-md-row flex-column">
            <div class="mb-2 mb-md-0">
                ©
                <script>
                    document.write(new Date().getFullYear());
                </script>
                , made with ❤️ by
                <a href="https://erlanggaonline.com/" target="_blank" class="footer-link fw-medium">ErlanggaOnline</a>
            </div>
        </div>
    </footer>
    <!-- / Footer -->

    <div class="content-backdrop fade"></div>
</div>
<!-- Content wrapper -->
</div>
<!-- / Layout page -->
</div>

<!-- Overlay -->
<div class="layout-overlay layout-menu-toggle"></div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Fungsi untuk menampilkan data dari database
function loadData() {
    $.ajax({
        type: 'GET',
        url: 'http://localhost:8080/api/buku',
        dataType: 'json',
        success: function(data) {
            var bukuData = '';
            $.each(data.buku, function(key, value) {
                bukuData += '<tr>';
                bukuData += '<td>' + value.kode_buku + '</td>';
                bukuData += '<td>' + value.judul_buku + '</td>';
                bukuData += '<td>' + value.pengarang + '</td>';
                bukuData += '<td>' + value.target_terbit + '</td>';
                bukuData += '<td>' + value.warna + '</td>';
                bukuData += '<td>';
                bukuData += '<div class="dropdown">';
                bukuData += '<button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">';
                bukuData += '<i class="bx bx-dots-horizontal-rounded"></i>';
                bukuData += '</button>';
                bukuData += '<div class="dropdown-menu">';
                bukuData += '<a class="dropdown-item" href="javascript:void(0);"><i class="bx bx-file me-2"></i> Detail</a>';
                bukuData += '<a class="dropdown-item dropdown-item-edit" href="javascript:void(0);" data-id_buku="' + value.id_buku + '"><i class="bx bx-edit-alt me-2"></i> Edit</a>';
                bukuData += '<a class="dropdown-item dropdown-item-delete" style="color: red;" href="javascript:void(0);" data-id_buku="' + value.id_buku + '"><i class="bx bx-trash me-2"></i> Delete</a>';
                bukuData += '</div>';
                bukuData += '</div>';
                bukuData += '</td>';
                bukuData += '</tr>';
            });
            $('#bukuData').html(bukuData);
        }
    });
}

// Fungsi untuk edit data
$(document).on('click', '.dropdown-item-edit', function() {
    var id_buku = $(this).data('id_buku'); // Ambil ID dari data-id_buku
    console.log('ID Buku:', id_buku); // Log ID untuk memverifikasi

    if (!id_buku) {
        console.log('ID tidak ditemukan!'); // Log jika ID undefined
        return; // Hentikan eksekusi jika ID tidak valid
    }

    $.ajax({
        type: 'GET',
        url: 'http://localhost:8080/api/buku/' + id_buku,
        dataType: 'json',
        success: function(response) {
            console.log('Respons API:', response); // Lihat respons API
            
            // Pastikan untuk memeriksa data_buku dari respons
            if (response.data_buku) {
                $('#kode_buku').val(response.data_buku.kode_buku);
                $('#judul_buku').val(response.data_buku.judul_buku);
                $('#pengarang').val(response.data_buku.pengarang);
                $('#target_terbit').val(response.data_buku.target_terbit);
                $('#warna').val(response.data_buku.warna);
            } else {
                console.log('Data buku tidak ditemukan'); // Log jika data_buku tidak ada
            }

            // Tampilkan modal setelah data berhasil diisi
            $('#editModal').modal('show');
        },
        error: function(xhr, status, error) {
            console.log('Error:', xhr.responseText); // Log jika terjadi error
        }
    });
});

// Fungsi untuk delete data
$(document).on('click', '.dropdown-item-delete', function() {
    var id_buku = $(this).data('id_buku');
    $.ajax({
        type: 'DELETE',
        url: 'http://localhost:8080/api/buku/' + id,
        success: function() {
            loadData();
        }
    });
});

// Fungsi untuk update data
$(document).on('click', '#btn-update', function() {
    var id = $('#id_buku').val();
    var kode_buku = $('#kode_buku').val();
    var judul_buku = $('#judul_buku').val();
    var pengarang = $('#pengarang').val();
    var target_terbit = $('#target_terbit').val();
    var warna = $('#warna').val();
    $.ajax({
        type: 'PUT',
        url: 'http://localhost:8080/api/buku/' + id,
        data: {
            kode_buku: kode_buku,
            judul_buku: judul_buku,
            pengarang: pengarang,
            target_terbit: target_terbit,
            warna: warna
        },
        success: function() {
            loadData();
            $('#modal-edit').modal('hide');
        }
    });
});

// Fungsi untuk tambah data
$(document).on('click', '#btn-tambah', function() {
    var kode_buku = $('#kode_buku_tambah').val();
    var judul_buku = $('#judul_buku_tambah').val();
    var pengarang = $('#pengarang_tambah').val();
    var target_terbit = $('#target_terbit_tambah').val();
    var warna = $('#warna_tambah').val();
    $.ajax({
        type: 'POST',
        url: 'http://localhost:8080/api/buku',
        data: {
            kode_buku: kode_buku,
            judul_buku: judul_buku,
            pengarang: pengarang,
            target_terbit: target_terbit,
            warna: warna
        },
        success: function() {
            loadData();
            $('#modal-tambah').modal('hide');
        }
    });
});

// Load data saat pertama kali halaman diakses
loadData();
</script>
<!-- / Layout wrapper -->
<?= $this->endSection(); ?>