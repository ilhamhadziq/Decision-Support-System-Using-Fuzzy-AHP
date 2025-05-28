<?php
include 'config.php';

// Retrieve criteria data from the database
$query = "SELECT id_kriteria, nama_kriteria FROM kriteria";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query error: " . mysqli_error($conn));
}

// Store criteria in an array
$kriteria = [];
while ($row = mysqli_fetch_assoc($result)) {
    $kriteria[] = $row;
}

// Total number of criteria
$total_kriteria = count($kriteria);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $matrix = $_POST['matrix'];

    // Initialize pairwise comparison matrix and column totals
    $pairwise_matrix = [];
    $column_totals = array_fill(0, $total_kriteria, 0);

    // Build the pairwise comparison matrix based on input
    for ($i = 0; $i < $total_kriteria; $i++) {
        for ($j = 0; $j < $total_kriteria; $j++) {
            if ($i == $j) {
                $pairwise_matrix[$i][$j] = 1;
            } elseif ($i < $j) {
                $pairwise_matrix[$i][$j] = $matrix[$i][$j];
                $pairwise_matrix[$j][$i] = 1 / $matrix[$i][$j];
            }
            $column_totals[$j] += $pairwise_matrix[$i][$j];
        }
    }

    // Normalize the matrix
    $normalized_matrix = [];
    $row_totals = [];
    for ($i = 0; $i < $total_kriteria; $i++) {
        $row_totals[$i] = 0;
        for ($j = 0; $j < $total_kriteria; $j++) {
            $normalized_matrix[$i][$j] = $pairwise_matrix[$i][$j] / $column_totals[$j];
            $row_totals[$i] += $normalized_matrix[$i][$j];
        }
    }

    // Calculate the relative weights (eigen vector)
    $eigen_vector = [];
    for ($i = 0; $i < $total_kriteria; $i++) {
        $eigen_vector[$i] = $row_totals[$i] / $total_kriteria;
    }

    // Calculate the consistency index (CI)
    $lambda_max = 0;
    for ($i = 0; $i < $total_kriteria; $i++) {
        $sum = 0;
        for ($j = 0; $j < $total_kriteria; $j++) {
            $sum += $pairwise_matrix[$i][$j] * $eigen_vector[$j];
        }
        $lambda_max += $sum / $eigen_vector[$i];
    }
    $lambda_max /= $total_kriteria;

    $CI = ($lambda_max - $total_kriteria) / ($total_kriteria - 1);

    // Consistency Ratio (CR)
    $RI_values = [0, 0, 0, 0.58, 0.90, 1.12, 1.24, 1.32, 1.41, 1.45];
    $RI = $RI_values[$total_kriteria];
    $CR = $CI / $RI;

    // Clear old data from `matrix_perbandingan` table
    $deleteQuery = "DELETE FROM matrix_perbandingan";
    if (!mysqli_query($conn, $deleteQuery)) {
        die("Error deleting old data: " . mysqli_error($conn));
    }
    
    // Insert pairwise comparison matrix into the database
    for ($i = 0; $i < $total_kriteria; $i++) {
        for ($j = 0; $j < $total_kriteria; $j++) {
            $kriteria_1 = $kriteria[$i]['id_kriteria'];
            $kriteria_2 = $kriteria[$j]['id_kriteria'];
            $nilai = $pairwise_matrix[$i][$j];
    
            $insertQuery = "INSERT INTO matrix_perbandingan (kriteria_1, kriteria_2, nilai)
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE nilai = VALUES(nilai)";
    
            $stmt = mysqli_prepare($conn, $insertQuery);
            //echo "Inserting SQL: " . $insertQuery . "<br>";
    
            // Debugging output untuk nilai yang akan di-insert
            //echo "Inserting: kriteria_1 = $kriteria_1, kriteria_2 = $kriteria_2, nilai = $nilai<br>";
    
            if ($stmt) {
                // Ganti 'iid' menjadi 'ssi' jika kriteria_1 dan kriteria_2 adalah VARCHAR
                mysqli_stmt_bind_param($stmt, "ssd", $kriteria_1, $kriteria_2, $nilai);
                if (!mysqli_stmt_execute($stmt)) {
                    echo "Execute failed: (" . mysqli_errno($conn) . ") " . mysqli_error($conn) . "<br>";
                }
                mysqli_stmt_close($stmt);
            } else {
                echo "Prepare failed: (" . mysqli_errno($conn) . ") " . mysqli_error($conn) . "<br>";
            }   
    }
    
    }

    // Commit if needed (useful if autocommit is off)
    mysqli_commit($conn);

    //echo "Pairwise comparison matrix successfully saved to the database.";

    // Close the database connection
    mysqli_close($conn);
}
?>


<!-- Normalization Matrix Table -->
<h4 class="text-center">Matrix Normalisasi </h4>
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
                    <td><?php echo round($normalized_matrix[$i][$j], 4); ?></td>
                <?php } ?>
            </tr>
        <?php } ?>
    </tbody>
</table>

<!-- Relative Weights Table -->
<h4 class="text-center">Bobot Relatif</h4>
<table class="table table-bordered text-center">
    <thead class="thead-dark">
        <tr>
            <th>Criteria ID</th>
            <th>Relative Weight</th>
        </tr>
    </thead>
    <tbody>
        <?php for ($i = 0; $i < $total_kriteria; $i++) { ?>
            <tr>
                <td><?php echo $kriteria[$i]['id_kriteria']; ?></td>
                <td><?php echo round($eigen_vector[$i], 4); ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<!-- Consistency Results Table -->
<h4 class="text-center">Konsistensi Rasio</h4>
<table class="table table-bordered text-center">
    <thead class="thead-dark">
        <tr>
            <th>Lambda Max</th>
            <th>CI</th>
            <th>RI</th>
            <th>CR</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><?php echo round($lambda_max, 4); ?></td>
            <td><?php echo round($CI, 4); ?></td>
            <td><?php echo $RI; ?></td>
            <td><?php echo round($CR, 4); ?> (<?php echo $CR < 0.1 ? 'Consistent' : 'Inconsistent'; ?>)</td>
        </tr>
    </tbody>
</table>
