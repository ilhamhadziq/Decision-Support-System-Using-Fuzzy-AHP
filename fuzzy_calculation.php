<?php
include 'config.php';

// Definisikan TFN_map sesuai dengan acuan AHP yang diberikan
$TFN_map = [
    '0.5' => [0.667, 1.000, 2.000],
    '0.333333' => [0.500, 0.667, 1.000],
    '0.25' => [0.429, 0.500, 0.714],
    '0.2' => [0.333, 0.400, 0.500],
    '0.166667' => [0.375, 0.500, 0.625],
    '0.142857' => [0.250, 0.333, 0.500],
    '0.125' => [0.417, 0.333, 0.500],
    '0.111111' => [0.105, 0.333, 0.211],
    '1' => [1.000, 1.000, 1.000],
    '2' => [0.500, 1.000, 1.500],
    '3' => [1.000, 1.500, 2.000],
    '4' => [1.500, 2.000, 2.500],
    '5' => [2.000, 2.500, 3.000],
    '6' => [2.500, 3.000, 3.500],
    '7' => [3.000, 3.500, 4.000],
    '8' => [3.500, 4.000, 4.500],
    '9' => [4.000, 4.500, 4.500]
];

// Ambil data dari database
$query = "SELECT * FROM matrix_perbandingan";
$result = mysqli_query($conn, $query);

// Inisialisasi array untuk menyimpan TFN
$tnf_matrix = [];

// Ubah nilai dari database ke bentuk TFN menggunakan TFN_map
while ($row = mysqli_fetch_assoc($result)) {
    $kriteria_1 = $row['kriteria_1'];
    $kriteria_2 = $row['kriteria_2'];
    $nilai = $row['nilai'];

    // Cek apakah nilai ada di TFN_map, jika tidak maka gunakan default [1, 1, 1]
    $tnf_values = isset($TFN_map[(string)$nilai]) ? $TFN_map[(string)$nilai] : [1, 1, 1];

    // Simpan ke dalam array
    $tnf_matrix[$kriteria_1][$kriteria_2] = $tnf_values;
}

// Tampilkan hasil dalam tabel HTML untuk verifikasi
echo '<div class="container mt-5">';
echo '<h3 class="text-center">Hasil Konversi Matriks Perbandingan Berpasangan ke TFN</h3>';
echo '<table class="table table-bordered table-striped text-center">';
echo '<thead class="thead-dark"><tr><th></th>';

// Header kolom untuk kriteria
foreach ($tnf_matrix as $kriteria => $values) {
    echo '<th colspan="3">' . $kriteria . '</th>';
}
echo '</tr><tr><th>Kriteria</th>';
foreach ($tnf_matrix as $kriteria => $values) {
    echo '<th>l</th><th>m</th><th>u</th>';
}
echo '</tr></thead><tbody>';

// Isi tabel TFN
foreach ($tnf_matrix as $kriteria_1 => $values) {
    echo '<tr><td><strong>' . $kriteria_1 . '</strong></td>';
    foreach ($values as $tnf) {
        echo '<td>' . $tnf[0] . '</td><td>' . $tnf[1] . '</td><td>' . $tnf[2] . '</td>';
    }
    echo '</tr>';
}

echo '</tbody></table>';
echo '</div>';


// Menghitung Tabel Jumlah berdasarkan nilai L, M, dan U dari setiap kriteria
$jumlahL = [];
$jumlahM = [];
$jumlahU = [];
$totalL = $totalM = $totalU = 0;

// Menghitung total untuk setiap kriteria dan kolom
foreach ($tnf_matrix as $kriteria => $values) {
    $sumL = 0;
    $sumM = 0;
    $sumU = 0;
    foreach ($values as $tnf) {
        $sumL += $tnf[0];
        $sumM += $tnf[1];
        $sumU += $tnf[2];
    }
    $jumlahL[$kriteria] = round($sumL, 2);
    $jumlahM[$kriteria] = round($sumM, 2);
    $jumlahU[$kriteria] = round($sumU, 2);
    
    $totalL += $sumL;
    $totalM += $sumM;
    $totalU += $sumU;
}

// Tabel Jumlah
echo '<div class="container mt-4">';
echo '<h3 class="text-center mb-4">Tabel Jumlah</h3>';
echo '<table class="table table-bordered text-center">';
echo '<thead class="table-dark">';
echo '<tr>';
echo '<th scope="col">Kriteria</th>';
echo '<th scope="col">L</th>';
echo '<th scope="col">M</th>';
echo '<th scope="col">U</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';

// Menampilkan nilai jumlah setiap kriteria
foreach ($jumlahL as $kriteria => $value) {
    echo '<tr>';
    echo '<td><strong>' . htmlspecialchars($kriteria) . '</strong></td>';
    echo '<td>' . number_format($jumlahL[$kriteria], 2) . '</td>';
    echo '<td>' . number_format($jumlahM[$kriteria], 2) . '</td>';
    echo '<td>' . number_format($jumlahU[$kriteria], 2) . '</td>';
    echo '</tr>';
}

// Menampilkan total di bagian akhir
echo '<tr class="table-secondary font-weight-bold">';
echo '<td><strong>TOTAL</strong></td>';
echo '<td><strong>' . number_format($totalL, 2) . '</strong></td>';
echo '<td><strong>' . number_format($totalM, 2) . '</strong></td>';
echo '<td><strong>' . number_format($totalU, 2) . '</strong></td>';
echo '</tr>';

echo '</tbody>';
echo '</table>';
echo '</div>';

// Menghitung tabel nilai sinteis berdasarkan logika yang diberikan
$sintesisL = [];
$sintesisM = [];
$sintesisU = [];

// Menghitung nilai sintesis untuk setiap kriteria
foreach ($jumlahL as $kriteria => $value) {
    $sintesisL[$kriteria] = round($jumlahL[$kriteria] * (1 / $totalU), 4);
    $sintesisM[$kriteria] = round($jumlahM[$kriteria] * (1 / $totalM), 4);
    $sintesisU[$kriteria] = round($jumlahU[$kriteria] * (1 / $totalL), 4);
}

// Tabel Nilai Sintesis
echo '<div class="container mt-4">';
echo '<h3 class="text-center mb-4">Tabel Nilai Sintesis</h3>';
echo '<table class="table table-bordered text-center">';
echo '<thead class="table-dark">';
echo '<tr>';
echo '<th scope="col">Kriteria</th>';
echo '<th scope="col">Sintesis L</th>';
echo '<th scope="col">Sintesis M</th>';
echo '<th scope="col">Sintesis U</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';

// Menampilkan nilai sintesis setiap kriteria
foreach ($sintesisL as $kriteria => $value) {
    echo '<tr>';
    echo '<td><strong>' . htmlspecialchars($kriteria) . '</strong></td>';
    echo '<td>' . number_format($sintesisL[$kriteria], 4) . '</td>';
    echo '<td>' . number_format($sintesisM[$kriteria], 4) . '</td>';
    echo '<td>' . number_format($sintesisU[$kriteria], 4) . '</td>';
    echo '</tr>';
}

echo '</tbody>';
echo '</table>';
echo '</div>';

// Derajat keanggotaan
$derajat_keanggotaan = [];

// Loop untuk setiap pasangan baris dan kolom kriteria
foreach ($sintesisL as $baris => $value_baris) {
    foreach ($sintesisL as $kolom => $value_kolom) {
        if ($baris == $kolom) {
            $derajat_keanggotaan[$baris][$kolom] = 1;
        } else {
            $L_baris = $sintesisL[$baris];
            $M_baris = $sintesisM[$baris];
            $U_baris = $sintesisU[$baris];
            
            $L_kolom = $sintesisL[$kolom];
            $M_kolom = $sintesisM[$kolom];
            $U_kolom = $sintesisU[$kolom];
            
            // Rumus derajat keanggotaan
            $result = ($L_kolom - $U_baris) / (($M_baris - $U_baris) - ($M_kolom - $L_kolom));
            if ($result > 1) {
                $result = 1;
            } elseif ($result < 0) {
                $result = 0;
            }
            
            $derajat_keanggotaan[$baris][$kolom] = round($result, 4);
        }
    }
}

// Tabel Derajat Keanggotaan
echo '<div class="container mt-4">';
echo '<h3 class="text-center mb-4">Tabel Derajat Keanggotaan</h3>';
echo '<table class="table table-bordered text-center">';
echo '<thead class="table-dark">';
echo '<tr><th>Kriteria</th>';

foreach ($sintesisL as $kriteria => $value) {
    echo '<th>' . htmlspecialchars($kriteria) . '</th>';
}
echo '</tr>';
echo '</thead>';
echo '<tbody>';

// Menampilkan nilai derajat keanggotaan setiap pasangan kriteria
foreach ($derajat_keanggotaan as $baris => $values) {
    echo '<tr>';
    echo '<td><strong>' . htmlspecialchars($baris) . '</strong></td>';
    foreach ($values as $nilai) {
        echo '<td>' . number_format($nilai, 4) . '</td>';
    }
    echo '</tr>';
}

echo '</tbody>';
echo '</table>';
echo '</div>';


// Inisialisasi array untuk bobot dan total bobot
$bobot = [];
$total_bobot = 0;

// Menghitung bobot sebagai nilai minimum dari setiap baris
foreach ($derajat_keanggotaan as $baris => $values) {
    $min_value = min($values);
    $bobot[$baris] = $min_value;
    $total_bobot += $min_value;
}

// Menghitung bobot ternormalisasi
$bobot_ternormalisasi = [];
foreach ($bobot as $baris => $value) {
    $bobot_ternormalisasi[$baris] = round($value / $total_bobot, 4);
}
// Tabel Bobot
echo '<div class="container mt-4">';
echo '<h3 class="text-center mb-4">Tabel Bobot</h3>';
echo '<table class="table table-bordered text-center">';
echo '<thead class="table-dark">';
echo '<tr>';
echo '<th scope="col">Kode</th>';
echo '<th scope="col">Bobot</th>';
echo '<th scope="col">Bobot Ternormalisasi</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';

// Menampilkan bobot dan bobot ternormalisasi setiap kode
foreach ($bobot as $baris => $value) {
    echo '<tr>';
    echo '<td><strong>' . htmlspecialchars($baris) . '</strong></td>';
    echo '<td>' . number_format($value, 4) . '</td>';
    echo '<td>' . number_format($bobot_ternormalisasi[$baris], 4) . '</td>';
    echo '</tr>';
}

echo '</tbody>';
echo '</table>';
echo '</div>';
// Menyimpan atau mengganti bobot dan bobot ternormalisasi ke dalam tabel 'bobot_kriteria' di database
foreach ($bobot_ternormalisasi as $baris => $bobot_value) {
    $bobot_asli = $bobot[$baris];
    
    // Query INSERT dengan ON DUPLICATE KEY UPDATE untuk menggantikan data jika sudah ada
    $insert_query = "INSERT INTO bobot_kriteria (id_kriteria, bobot, bobot_ternormalisasi) 
                     VALUES ('$baris', '$bobot_asli', '$bobot_value') 
                     ON DUPLICATE KEY UPDATE 
                     bobot = '$bobot_asli', bobot_ternormalisasi = '$bobot_value'";
    mysqli_query($conn, $insert_query);
}

// Tutup koneksi database
mysqli_close($conn);
?>

