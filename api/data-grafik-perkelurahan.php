<?php
require '../koneksi.php';
header('Content-Type: application/json');

try {
    // Query untuk mengelompokkan data per kelurahan dan gender
    // Menggunakan SUM(CASE...) agar hasil L dan P muncul dalam satu baris per kelurahan
    $sql = "SELECT 
                kelurahan,
                SUM(CASE WHEN gender = 'L' THEN 1 ELSE 0 END) as total_l,
                SUM(CASE WHEN gender = 'P' THEN 1 ELSE 0 END) as total_p
            FROM data_penduduk 
            GROUP BY kelurahan 
            ORDER BY kelurahan ASC";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Siapkan array untuk format Highcharts
    $categories = []; // Daftar Nama Kelurahan
    $dataL = [];      // Data Jumlah Laki-laki
    $dataP = [];      // Data Jumlah Perempuan

    foreach ($results as $row) {
        $categories[] = $row['kelurahan'];
        $dataL[] = (int) $row['total_l'];
        $dataP[] = (int) $row['total_p'];
    }

    echo json_encode([
        'status' => 'success',
        'categories' => $categories,
        'series' => [
            ['name' => 'Laki-laki', 'data' => $dataL, 'color' => '#0d6efd'], // Biru
            ['name' => 'Perempuan', 'data' => $dataP, 'color' => '#d63384']  // Pink
        ]
    ]);

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>