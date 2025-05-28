<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

include 'config.php';

// Ambil data dari POST dan decode JSON
$nilaiKriteria = isset($_POST['export_data']) ? json_decode($_POST['export_data'], true) : null;

if (!$nilaiKriteria) {
    echo "Tidak ada data untuk diekspor.";
    exit;
}

// Ambil data bobot dan alternatif dari database
$alternativesStmt = $conn->query("SELECT id_alternatif, nama_alternatif, kamar FROM alternatif ORDER BY CAST(SUBSTRING(id_alternatif, 2) AS UNSIGNED)");
$alternatives = $alternativesStmt->fetch_all(MYSQLI_ASSOC);

$weightsStmt = $conn->query("SELECT id_kriteria, bobot_ternormalisasi FROM bobot_kriteria");
$weights = [];
$index = 1;
while ($row = $weightsStmt->fetch_assoc()) {
    $weights[$row['id_kriteria']] = [
        'label' => 'K' . $index++,
        'bobot' => $row['bobot_ternormalisasi']
    ];
}

// Buat Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Hasil Perhitungan');

// Header tabel
$sheet->setCellValue('A1', 'ID Alternatif')
    ->setCellValue('B1', 'Nama')
    ->setCellValue('C1', 'Kamar')
    ->setCellValue('D1', 'Total Nilai')
    ->setCellValue('E1', 'Kelas');

// Isi data
$rowNumber = 2;
foreach ($nilaiKriteria as $id_alternatif => $kriteria) {
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
        $sheet->setCellValue("A{$rowNumber}", $altInfo['id_alternatif'])
            ->setCellValue("B{$rowNumber}", $altInfo['nama_alternatif'])
            ->setCellValue("C{$rowNumber}", $altInfo['kamar'])
            ->setCellValue("D{$rowNumber}", number_format($totalScore, 2))
            ->setCellValue("E{$rowNumber}", $kelas);
        $rowNumber++;
    }
}

// Simpan ke file Excel
$writer = new Xlsx($spreadsheet);
$fileName = 'laporan-hasil-perhitungan.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"{$fileName}\"");
$writer->save('php://output');
exit;
?>
