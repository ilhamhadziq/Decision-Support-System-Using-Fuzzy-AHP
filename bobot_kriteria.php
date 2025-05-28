<?php
include 'config.php'; // File untuk koneksi ke database

// Query untuk mengambil data dari tabel kriteria
$query = "SELECT id_kriteria, nama_kriteria FROM kriteria";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query error: " . mysqli_error($conn));
}

// Ambil semua kriteria dalam array
$kriteria = [];
while ($row = mysqli_fetch_assoc($result)) {
    $kriteria[] = $row;
}

// Total jumlah kriteria
$total_kriteria = count($kriteria);

// Query untuk mengambil data dari tabel matrix_perbandingan
$query_matrix = "SELECT * FROM matrix_perbandingan";
$result_matrix = mysqli_query($conn, $query_matrix);

$matrix_data = [];
while ($row = mysqli_fetch_assoc($result_matrix)) {
    $matrix_data[$row['kriteria_1']][$row['kriteria_2']] = $row['nilai'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matriks Perbandingan Berpasangan</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-4">
    <h2 class="text-center">List Daftar Kriteria</h2>
    <table class="table table-bordered text-center">
        <thead class="thead-dark">
            <tr>
                <th style="width: 100px;">ID</th>
                <th style="width: 800px;">NAMA</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($kriteria as $row) { ?>
                <tr>
                    <td><?php echo $row['id_kriteria']; ?></td>
                    <td><?php echo $row['nama_kriteria']; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<div class="container mt-5">
    <h2 class="text-center">Matriks Perbandingan Berpasangan</h2>
    <form id="matrixForm" method="post">
        <table class="table table-bordered text-center">
            <thead class="thead-dark">
                <tr>
                    <th></th>
                    <?php foreach ($kriteria as $k) { ?>
                        <th><?php echo $k['id_kriteria']; ?></th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php for ($i = 0; $i < $total_kriteria; $i++) { ?>
                    <tr>
                        <td><strong><?php echo $kriteria[$i]['id_kriteria']; ?></strong></td>
                        <?php for ($j = 0; $j < $total_kriteria; $j++) { ?>
                            <td>
                                <?php if ($i == $j) { ?>
                                    1
                                <?php } elseif ($i < $j) { 
                                    $value = isset($matrix_data[$kriteria[$i]['id_kriteria']][$kriteria[$j]['id_kriteria']]) 
                                        ? $matrix_data[$kriteria[$i]['id_kriteria']][$kriteria[$j]['id_kriteria']] 
                                        : ''; 
                                ?>
                                    <input type="number" min="1" max="9" class="form-control" name="matrix[<?php echo $i; ?>][<?php echo $j; ?>]" value="<?php echo $value; ?>" required>
                                <?php } else { 
                                    // Hitung kebalikan jika ada nilai yang valid
                                    $reciprocal_value = isset($matrix_data[$kriteria[$j]['id_kriteria']][$kriteria[$i]['id_kriteria']]) 
                                        ? (1 / $matrix_data[$kriteria[$j]['id_kriteria']][$kriteria[$i]['id_kriteria']]) 
                                        : 0;
                                ?>
                                    <input type="text" class="form-control" readonly id="reciprocal_<?php echo $i; ?>_<?php echo $j; ?>" value="<?php echo ($reciprocal_value != 0) ? number_format($reciprocal_value, 2) : ''; ?>">
                                <?php } ?>
                            </td>
                        <?php } ?>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>

    <!-- Div untuk menampilkan hasil -->
    <div id="result" class="mt-5"></div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<!-- JavaScript untuk menghitung kebalikan otomatis -->
<script>
document.addEventListener('input', function(event) {
    const target = event.target;
    
    if (target.tagName === 'INPUT' && target.type === 'number') {
        const matrixName = target.name;
        const [i, j] = matrixName.match(/\d+/g);
        const reciprocalInput = document.getElementById(`reciprocal_${j}_${i}`);
        
        if (reciprocalInput) {
            const value = parseFloat(target.value);
            if (value && value !== 0) { // Pastikan nilai valid dan bukan nol
                reciprocalInput.value = (1 / value).toFixed(2);
            } else {
                reciprocalInput.value = ''; // Kosongkan jika tidak valid
            }
        }
    }
});
</script>

<script>
// Menangani submit form dengan AJAX
document.getElementById('matrixForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Mencegah submit form secara tradisional

    const formData = new FormData(this);

    // Mengirim data menggunakan AJAX
    fetch('proses_matrix.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        // Tampilkan hasil di div #result
        document.getElementById('result').innerHTML = data;

        // Buat pesan dan tombol di bawah hasil yang ditampilkan
        const additionalContent = `
            <div class="mt-3 text-danger">
                <p>Jika nilai CR sudah konsisten, lanjutkan ke tahap selanjutnya. Jika belum, atur kembali.</p>
                <button onclick="aturKembali()" class="btn btn-secondary">Atur Kembali</button>
                <button onclick="lanjutkanPerhitungan()" class="btn btn-primary" id="lanjutButton">Lanjutkan Perhitungan Fuzzy</button>
            </div>
        `;

        // Tambahkan elemen tambahan ke dalam div #result
        document.getElementById('result').insertAdjacentHTML('beforeend', additionalContent);
    })
    .catch(error => console.error('Error:', error));
});

function aturKembali() {
    location.reload(); // Atur kembali dengan refresh halaman
}

function lanjutkanPerhitungan() {
    // Cek jika tombol sudah pernah diklik sebelumnya
    const lanjutButton = document.getElementById('lanjutButton');
    if (lanjutButton.disabled) return; // Jika tombol sudah dinonaktifkan, hentikan fungsi ini

    lanjutButton.disabled = true; // Nonaktifkan tombol setelah klik pertama

    const formData = new FormData();
    formData.append('action', 'lanjutkan_fuzzy');

    fetch('fuzzy_calculation.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        document.getElementById('result').innerHTML += `
            <h4 class="text-center mt-5">Hasil Fuzzy Triangular Number (TFN)</h4>
            ${data}
        `;
    })
    .catch(error => console.error('Error:', error));
}
</script>

</body>
</html>
