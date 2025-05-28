<?php
include 'config.php';

// Cek apakah ada input pencarian
$searchText = isset($_POST['searchText']) ? $_POST['searchText'] : '';

// Query untuk mengambil data alternatif dengan filter berdasarkan pencarian
// Query untuk mengambil data alternatif dengan filter berdasarkan pencarian
$query = "SELECT id_alternatif, nama_alternatif, kamar FROM alternatif";
if ($searchText != '') {
    $query .= " WHERE nama_alternatif LIKE '%" . mysqli_real_escape_string($conn, $searchText) . "%'";
}


// Urutkan data berdasarkan bagian angka setelah 'A' dalam id_alternatif
$query .= " ORDER BY CAST(SUBSTRING(id_alternatif, 2) AS UNSIGNED)";

$result = mysqli_query($conn, $query);
if (!$result) {
    die("Query error: " . mysqli_error($conn));
}

$lastKodeQuery = "SELECT id_alternatif FROM alternatif ORDER BY CAST(SUBSTRING(id_alternatif, 2) AS UNSIGNED) DESC LIMIT 1";
$lastKodeResult = mysqli_query($conn, $lastKodeQuery);
if ($lastKodeRow = mysqli_fetch_assoc($lastKodeResult)) {
    $lastKode = $lastKodeRow['id_alternatif'];
    $numberPart = (int) substr($lastKode, 1); // Ambil angka dari kode
    $newKode = 'A' . ($numberPart + 1); // Membuat kode baru
} else {
    $newKode = 'A1'; // Jika belum ada kode, mulai dari A1
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data alternatif</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
</head>
<body>

<!-- Pencarian, Tambah Data, Reset -->
<div class="container mt-4">
    <div class="row mb-3 align-items-end">
        <div class="col-lg-6">
            <input type="text" id="searchText" class="form-control" placeholder="Masukkan Nama Santri">
        </div>
        <div class="col-lg-6 d-flex justify-content-end">
            <button class="btn btn-success mr-2" onclick="search()">Search</button>
            <button class="btn btn-primary mr-2" data-bs-toggle="modal" data-bs-target="#addModal">+ Add Record</button>
            <button class="btn btn-info" onclick="resetSearch()">Reset</button>
        </div>
    </div>

    <!-- Tabel alternatif -->
    <div class="row">
        <div class="col-lg-12">
        <table class="table table-bordered table-striped w-100">
                <thead class="thead-dark">
                    <tr>
                        <th>Kode</th>
                        <th style="width: 800px;">Nama alternatif</th>
                        <th style="width: 100px;">kamar</th> <!-- Kolom kamar -->
                        <th style="width: 300px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?php echo $row['id_alternatif']; ?></td>
                            <td><?php echo $row['nama_alternatif']; ?></td>
                            <td><?php echo $row['kamar']; ?></td> <!-- Menampilkan data kamar -->
                            <td>
                                <button class="btn btn-info btn-sm" onclick="editRecord('<?php echo $row['id_alternatif']; ?>', '<?php echo $row['nama_alternatif']; ?>', '<?php echo $row['kamar']; ?>')">
                                <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="deleteRecord('<?php echo $row['id_alternatif']; ?>')">
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
                    <h5 class="modal-title" id="addModalLabel">Add New alternatif</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Field id_alternatif (Otomatis dari PHP) -->
                    <div class="form-group">
                        <label for="id_alternatif">Kode alternatif</label>
                        <input type="text" class="form-control" id="id_alternatif" name="id_alternatif" value="<?php echo $newKode; ?>" readonly>
                    </div>
                    <!-- Field nama_alternatif -->
                    <div class="form-group">
                        <label for="nama_alternatif">Nama alternatif</label>
                        <input type="text" class="form-control" id="nama_alternatif" name="nama_alternatif" required>
                    </div>
                    <div class="form-group">
                        <label for="kamar">Kamar</label>
                        <input type="text" class="form-control" id="kamar" name="kamar" required>
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
        fetch('insert_alternatif.php', {
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
                    <h5 class="modal-title" id="editModalLabel">Edit alternatif</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    
                </div>
                <div class="modal-body">
                    <!-- Field id_alternatif (Read-only) -->
                    <div class="form-group">
                        <label for="edit_id_alternatif">Kode alternatif</label>
                        <input type="text" class="form-control" id="edit_id_alternatif" name="id_alternatif" readonly>
                    </div>
                    <!-- Field nama_alternatif -->
                    <div class="form-group">
                        <label for="edit_nama_alternatif">Nama alternatif</label>
                        <input type="text" class="form-control" id="edit_nama_alternatif" name="nama_alternatif" required>
                    </div>
                    <!-- Field kamar -->
                    <div class="form-group">
                        <label for="edit_kamar">Kamar</label>
                        <input type="text" class="form-control" id="edit_kamar" name="kamar" required>
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
       function editRecord(id_alternatif, nama_alternatif, kamar) {
        // Isi modal dengan data yang dikirim
        document.getElementById('edit_id_alternatif').value = id_alternatif;
        document.getElementById('edit_nama_alternatif').value = nama_alternatif;
        document.getElementById('edit_kamar').value = kamar;

        // Tampilkan modal
        $('#editModal').modal('show');
    }

    // Submit form edit menggunakan AJAX
    document.getElementById('editForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Mencegah form submit biasa

        const formData = new FormData(this);

        fetch('update_alternatif.php', {
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
    function deleteRecord(id_alternatif) {
        if (confirm('Apakah Anda yakin ingin menghapus alternatif ini?')) {
            fetch('delete_alternatif.php?id=' + id_alternatif, {
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
