<?php

include 'config.php'; // Memanggil file konfigurasi untuk koneksi database

// Menyimpan data ke database saat form dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nilaiKriteria = $_POST['nilai_kriteria'] ?? [];

    // Menyimpan data ke tabel nilai_dropdown
    foreach ($nilaiKriteria as $id_alternatif => $kriteria) {
        foreach ($kriteria as $id_kriteria => $nilai) {
            $stmt = $conn->prepare("
                INSERT INTO nilai_dropdown (id_alternatif, id_kriteria, nilai)
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE nilai = VALUES(nilai)
            ");
            $stmt->bind_param('ssi', $id_alternatif, $id_kriteria, $nilai);
            $stmt->execute();
            $stmt->close();
        }
    }
}

// Mengambil data dari database untuk mengisi dropdown
$selectedValues = [];
$result = $conn->query("SELECT id_alternatif, id_kriteria, nilai FROM nilai_dropdown");
while ($row = $result->fetch_assoc()) {
    $selectedValues[$row['id_alternatif']][$row['id_kriteria']] = $row['nilai'];
}

// Mengambil data alternatif dengan pengurutan yang benar
$alternativesStmt = $conn->query("SELECT id_alternatif, nama_alternatif, kamar FROM alternatif ORDER BY CAST(SUBSTRING(id_alternatif, 2) AS UNSIGNED)");
$alternatives = $alternativesStmt->fetch_all(MYSQLI_ASSOC);

// Mengambil bobot ternormalisasi
$weightsStmt = $conn->query("SELECT id_kriteria, bobot_ternormalisasi FROM bobot_kriteria");
$weights = [];
$index = 1;
while ($row = $weightsStmt->fetch_assoc()) {
    $weights[$row['id_kriteria']] = [
        'label' => 'K' . $index++,
        'bobot' => $row['bobot_ternormalisasi']
    ];
}

// Mendefinisikan pilihan kriteria dan nilai numeriknya
$criteriaOptions = [
    'SB' => 100,
    'B' => 80,
    'C' => 60,
    'K' => 40,
    'SK' => 20,
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Perhitungan Alternatif</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table th, .table td { padding: 0.3rem; font-size: 0.9rem; }
        .form-select { padding: 0.2rem 0.4rem; font-size: 0.85rem; }
    </style>
</head>
<body>
<div class="container mt-5">
    <!-- Tabel Keterangan Nilai -->
    <h2 class="text-center mb-4">Keterangan Nilai</h2>
    <table class="table table-bordered table-striped table-sm">
        <thead class="thead-dark">
            <tr>
                <th>Kode</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>SK</td>
                <td>Sangat Kurang</td>
            </tr>
            <tr>
                <td>K</td>
                <td>Kurang</td>
            </tr>
            <tr>
                <td>C</td>
                <td>Cukup</td>
            </tr>
            <tr>
                <td>B</td>
                <td>Baik</td>
            </tr>
            <tr>
                <td>SB</td>
                <td>Sangat Baik</td>
            </tr>
        </tbody>
    </table>
    <h2 class="text-center mb-4">Perhitungan Alternatif</h2>
    <form method="post" action="">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-sm">
                <thead class="thead-dark">
                    <tr>
                        <th style="width: 200px;">ID Alternatif</th>
                        <th style="width: 600px;">Nama</th>
                        <th>Kamar</th>
                        <?php foreach ($weights as $weight): ?>
                            <th><?php echo $weight['label']; ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($alternatives as $alt): ?>
                        <tr>
                            <td class="text-center align-middle"><?php echo $alt['id_alternatif']; ?></td>
                            <td><?php echo $alt['nama_alternatif']; ?></td>
                            <td><?php echo $alt['kamar']; ?></td>
                            <?php foreach ($weights as $id_kriteria => $weight): ?>
                                <td>
                                    <select name="nilai_kriteria[<?php echo $alt['id_alternatif']; ?>][<?php echo $id_kriteria; ?>]" class="form-select">
                                        <?php foreach ($criteriaOptions as $label => $value): ?>
                                            <option value="<?php echo $value; ?>" 
                                                <?php echo isset($selectedValues[$alt['id_alternatif']][$id_kriteria]) && $selectedValues[$alt['id_alternatif']][$id_kriteria] == $value ? 'selected' : ''; ?>>
                                                <?php echo $label; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="text-center">
            <button type="submit" name="calculate" class="btn btn-primary btn-sm">Hitung</button>
        </div>
    </form>

    <?php
    // Hapus data sesi jika tombol reset diklik
    // if (isset($_POST['reset'])) {
    //     unset($_SESSION['nilai_kriteria']);
    // }

    // Bagian perhitungan hasil
    if (isset($_POST['calculate'])) {
        echo "<h3 class='text-center mt-4'>Hasil Perhitungan</h3>";
        echo "<div class='table-responsive'>";
        echo "<table class='table table-bordered table-sm'>";
        echo "<thead class='thead-light'><tr><th>ID</th><th>Nama</th><th>Kamar</th><th>Total Nilai</th><th>Kelas</th></tr></thead><tbody>";

        foreach ($_POST['nilai_kriteria'] as $id_alternatif => $kriteria) {
            $totalScore = 0;
            foreach ($kriteria as $id_kriteria => $nilai) {
                $bobot = $weights[$id_kriteria]['bobot'] ?? 0;
                $totalScore += $nilai * $bobot;
            }

            // Menentukan kelas berdasarkan total nilai
            $kelas = '';
            if ($totalScore >= 91) {
                $kelas = 'MUSTAHIQ';
            } elseif ($totalScore >= 76) {
                $kelas = 'A';
            } elseif ($totalScore >= 55) {
                $kelas = 'B';
            } elseif ($totalScore >= 21) {
                $kelas = 'C';
            } else {
                $kelas = 'D';
            }

            $altInfo = array_filter($alternatives, fn($alt) => $alt['id_alternatif'] == $id_alternatif);
            $altInfo = reset($altInfo);

            if ($altInfo) {
                echo "<tr>";
                echo "<td>{$altInfo['id_alternatif']}</td>";
                echo "<td>{$altInfo['nama_alternatif']}</td>";
                echo "<td>{$altInfo['kamar']}</td>";
                echo "<td>" . number_format($totalScore, 2) . "</td>";
                echo "<td>{$kelas}</td>";
                echo "</tr>";
            } else {
                echo "<tr><td colspan='5'>Data tidak ditemukan untuk ID: $id_alternatif</td></tr>";
            }
        }
        echo "</tbody></table></div>";

        // Tambahkan hidden input untuk menyimpan nilai kriteria dalam JSON
        echo '<form id="exportForm" method="post" action="export_excel.php">';
        echo '<input type="hidden" name="export_data" value="' . htmlspecialchars(json_encode($_POST['nilai_kriteria'])) . '">';
        echo '<div class="text-center mt-3">';
        echo '<button type="submit" class="btn btn-success btn-sm">Export to Excel</button>';
        echo '</div>';
        echo '</form>';
    }
    ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
