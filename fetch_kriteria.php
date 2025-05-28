<?php
include 'config.php';

// Query untuk mengambil data kriteria
$query = "SELECT id_kriteria, nama_kriteria FROM kriteria";
$result = mysqli_query($conn, $query);

// Tampilkan data dalam bentuk HTML tabel
while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>" . $row['id_kriteria'] . "</td>";
    echo "<td>" . $row['nama_kriteria'] . "</td>";
    echo "<td>";
    echo "<a href='update_kriteria.php?id=" . $row['id_kriteria'] . "' class='btn btn-info btn-sm'><i class='fas fa-edit'></i> Edit</a> ";
    echo "<a href='delete_kriteria.php?id=" . $row['id_kriteria'] . "' class='btn btn-danger btn-sm'><i class='fas fa-trash-alt'></i> Remove</a>";
    echo "</td>";
    echo "</tr>";
}
?>
