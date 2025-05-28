<?php
include 'config.php';

// Cek apakah ada input pencarian
$searchText = isset($_POST['searchText']) ? $_POST['searchText'] : '';

// Query untuk mengambil data kriteria dengan filter berdasarkan pencarian
$query = "SELECT id_kriteria, nama_kriteria FROM kriteria";
if ($searchText != '') {
    $query .= " WHERE nama_kriteria LIKE '%" . mysqli_real_escape_string($conn, $searchText) . "%'";
}

$result = mysqli_query($conn, $query);
if (!$result) {
    die("Query error: " . mysqli_error($conn));
}
$lastKodeQuery = "SELECT id_kriteria FROM kriteria ORDER BY id_kriteria DESC LIMIT 1";
$lastKodeResult = mysqli_query($conn, $lastKodeQuery);
if ($lastKodeRow = mysqli_fetch_assoc($lastKodeResult)) {
    // Mengambil kode terakhir dan menghitung kode berikutnya
    $lastKode = $lastKodeRow['id_kriteria'];
    $numberPart = (int) substr($lastKode, 1); // Ambil angka dari kode, misal K7 jadi 7
    $newKode = 'K' . ($numberPart + 1); // Membuat kode baru, misal K8
} else {
    // Jika belum ada kode, mulai dari K1
    $newKode = 'K1';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Kriteria</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
</head>
<body>

<!-- Pencarian, Tambah Data, Reset -->
<div class="container mt-4">
    <div class="row mb-3 align-items-end">
        <div class="col-lg-6">
            <input type="text" id="searchText" class="form-control" placeholder="Masukkan Nama Kriteria">
        </div>
        <div class="col-lg-6 d-flex justify-content-end">
            <button class="btn btn-success mr-2" onclick="search()">Search</button>
            <button class="btn btn-primary mr-2" data-bs-toggle="modal" data-bs-target="#addModal">+ Add Record</button>
            <button class="btn btn-info" onclick="resetSearch()">Reset</button>
        </div>
    </div>

    <!-- Tabel Kriteria -->
    <div class="row">
        <div class="col-lg-12">
            <table class="table table-bordered table-striped w-100">
                <thead class="thead-dark">
                    <tr>
                        <th>Kode</th>
                        <th style="width: 800px;">Nama Kriteria</th>
                        <th style="width: 200px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?php echo $row['id_kriteria']; ?></td>
                            <td><?php echo $row['nama_kriteria']; ?></td>
                            <td>
                                <button class="btn btn-info btn-sm" onclick="editRecord('<?php echo $row['id_kriteria']; ?>', '<?php echo $row['nama_kriteria']; ?>')">
                                  <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="deleteRecord('<?php echo $row['id_kriteria']; ?>')">
                                    <i class="fas fa-trash-alt"></i> Remove
                                </button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal untuk Add Record -->
<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="addForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Add New Kriteria</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Field id_kriteria (Otomatis dari PHP) -->
                    <div class="form-group">
                        <label for="id_kriteria">Kode Kriteria</label>
                        <input type="text" class="form-control" id="id_kriteria" name="id_kriteria" value="<?php echo $newKode; ?>" readonly>
                    </div>
                    <!-- Field nama_kriteria -->
                    <div class="form-group">
                        <label for="nama_kriteria">Nama Kriteria</label>
                        <input type="text" class="form-control" id="nama_kriteria" name="nama_kriteria" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Insert</button>
                </div>
            </form>
        </div>
    </div>
</div>



<script>
    // Event listener untuk form add record
    document.getElementById('addForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Mencegah form submit secara tradisional

        // Ambil data dari form
        const formData = new FormData(this);

        // Kirim data menggunakan AJAX
        fetch('insert_kriteria.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            // Setelah berhasil menambahkan data
            alert('Data berhasil ditambahkan');
            
            // Reset form untuk data berikutnya
            document.getElementById('addForm').reset();
            
            // Tutup modal setelah data ditambahkan
            $('#addModal').modal('hide');
            
            // Reload atau perbarui tabel data secara otomatis
            location.reload(); // Ini akan me-reload halaman untuk memperbarui tabel
        })
        .catch(error => console.error('Error:', error));
    });
</script>



<!-- Modal untuk Edit Record -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="editForm" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Kriteria</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    
                </div>
                <div class="modal-body">
                    <!-- Field id_kriteria (Read-only) -->
                    <div class="form-group">
                        <label for="edit_id_kriteria">Kode Kriteria</label>
                        <input type="text" class="form-control" id="edit_id_kriteria" name="id_kriteria" readonly>
                    </div>
                    <!-- Field nama_kriteria -->
                    <div class="form-group">
                        <label for="edit_nama_kriteria">Nama Kriteria</label>
                        <input type="text" class="form-control" id="edit_nama_kriteria" name="nama_kriteria" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- jQuery dan Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<!-- JavaScript untuk mengirim data pencarian ke server -->
<script>
    function search() {
        const formData = new FormData();
        formData.append('searchText', document.getElementById('searchText').value);

        fetch('', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            document.querySelector('table tbody').innerHTML = data.match(/<tbody>([\s\S]*?)<\/tbody>/)[1];
        });
    }

    function resetSearch() {
        document.getElementById('searchText').value = '';
        search();
    }

   ;
</script>

<script>
    function editRecord(id_kriteria, nama_kriteria) {
        // Isi modal dengan data yang dikirim
        document.getElementById('edit_id_kriteria').value = id_kriteria;
        document.getElementById('edit_nama_kriteria').value = nama_kriteria;

        // Tampilkan modal
        $('#editModal').modal('show');
    }

    // Submit form edit menggunakan AJAX
    document.getElementById('editForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Mencegah form submit biasa

        const formData = new FormData(this);

        fetch('update_kriteria.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            alert('Data berhasil diperbarui');
            location.reload(); // Muat ulang halaman setelah edit berhasil
        })
        .catch(error => console.error('Error:', error));
    });
</script>

<script>
    function deleteRecord(id_kriteria) {
        if (confirm('Apakah Anda yakin ingin menghapus kriteria ini?')) {
            fetch('delete_kriteria.php?id=' + id_kriteria, {
                method: 'GET'
            })
            .then(response => response.text())
            .then(data => {
                alert('Data berhasil dihapus');
                location.reload(); // Muat ulang halaman setelah delete berhasil
            })
            .catch(error => console.error('Error:', error));
        }
    }
</script>


</body>
</html>
