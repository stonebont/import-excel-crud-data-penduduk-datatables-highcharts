<?php
require '../koneksi.php';
header('Content-Type: application/json');
error_reporting(0);

try {
    // Ambil semua data tempat lahir, dikelompokkan dan diurutkan dari terbanyak
    $sql = "SELECT UPPER(tempat_lahir) as nama_kota, COUNT(*) as total 
            FROM data_penduduk 
            WHERE tempat_lahir IS NOT NULL AND tempat_lahir != ''
            GROUP BY tempat_lahir 
            ORDER BY total DESC";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $categories = [];
    $seriesData = [];
    
    $limit = 20; // Batas Top 20
    $count = 0;
    $totalLainnya = 0;

    foreach ($results as $row) {
        if ($count < $limit) {
            // Masukkan ke Top 10
            $categories[] = $row['nama_kota']; // Nama Kota
            $seriesData[] = (int) $row['total']; // Jumlah
        } else {
            // Jumlahkan sisanya ke variabel penampung
            $totalLainnya += (int) $row['total'];
        }
        $count++;
    }

    // Jika ada data sisa, tambahkan kategori "LAINNYA"
    if ($totalLainnya > 0) {
        $categories[] = 'LAINNYA';
        $seriesData[] = $totalLainnya;
    }

    echo json_encode([
        'status' => 'success',
        'categories' => $categories,
        'data' => $seriesData
    ]);

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>