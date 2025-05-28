<?php
include 'config.php';

// Cek apakah ada input pencarian
$searchText = isset($_POST['searchText']) ? $_POST['searchText'] : '';

// Query untuk mengambil data users dengan filter berdasarkan pencarian
$query = "SELECT id, username, password, role FROM users";
if ($searchText != '') {
    $query .= " WHERE username LIKE '%" . mysqli_real_escape_string($conn, $searchText) . "%'";
}
$query .= " ORDER BY id";

$result = mysqli_query($conn, $query);
if (!$result) {
    die("Query error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Users</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
</head>
<body>

<!-- Pencarian, Tambah Data, Reset -->
<div class="container mt-4">
    <div class="row mb-3 align-items-end">
        <div class="col-lg-6">
            <input type="text" id="searchText" class="form-control" placeholder="Masukkan Username">
        </div>
        <div class="col-lg-6 d-flex justify-content-end">
            <button class="btn btn-success mr-2" onclick="search()">Search</button>
            <button class="btn btn-primary mr-2" data-bs-toggle="modal" data-bs-target="#addModal">+ Add User</button>
            <button class="btn btn-info" onclick="resetSearch()">Reset</button>
        </div>
    </div>

    <!-- Tabel Users -->
    <div class="row">
        <div class="col-lg-12">
            <table class="table table-bordered table-striped w-100">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th style="width: 700px;">Username</th>
                        <th style="width: 100px;">Password</th>
                        <th style="width: 100px;">Role</th>
                        <th style="width: 300px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['username']; ?></td>
                            <td><?php echo $row['password']; ?></td>
                            <td><?php echo $row['role']; ?></td>
                            <td>
                                <button class="btn btn-info btn-sm" onclick="editRecord('<?php echo $row['id']; ?>', '<?php echo $row['username']; ?>', '<?php echo $row['password']; ?>', '<?php echo $row['role']; ?>')">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="deleteRecord('<?php echo $row['id']; ?>')">
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

<!-- Modal untuk Add User -->
<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="addForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="role">Role</label>
                        <select class="form-control" id="role" name="role" required>
                            <option value="admin">Admin</option>
                            <option value="user">User</option>
                        </select>
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
        fetch('insert_users.php', {
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

<!-- Modal untuk Edit User -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="editForm" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="form-group">
                        <label for="edit_username">Username</label>
                        <input type="text" class="form-control" id="edit_username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_password">Password</label>
                        <input type="text" class="form-control" id="edit_password" name="password">
                    </div>
                    <div class="form-group">
                        <label for="edit_role">Role</label>
                        <select class="form-control" id="edit_role" name="role" required>
                            <option value="admin">Admin</option>
                            <option value="user">User</option>
                        </select>
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
       function editRecord(id, username, password, role) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_username').value = username;
        document.getElementById('edit_password').value = password;
        document.getElementById('edit_role').value = role;

        $('#editModal').modal('show');
    }


    // Submit form edit menggunakan AJAX
    document.getElementById('editForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Mencegah form submit biasa

        const formData = new FormData(this);

        fetch('update_users.php', {
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
    function deleteRecord(id) {
        if (confirm('Apakah Anda yakin ingin menghapus users ini?')) {
            fetch('delete_users.php?id=' + id, {
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
