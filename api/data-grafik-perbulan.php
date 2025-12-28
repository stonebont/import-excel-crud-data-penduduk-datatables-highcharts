<?php
require '../koneksi.php'; // Pastikan koneksi.php ada dan benar
header('Content-Type: application/json');

error_reporting(0); // Matikan error reporting agar tidak merusak format JSON

try {
    // Inisialisasi array untuk 12 bulan dengan nilai 0
    $dataBulan = array_fill(1, 12, 0); 

    // Query menghitung jumlah per bulan lahir
    $sqlBulan = "SELECT MONTH(tgl_lahir) as bulan, COUNT(*) as total 
                 FROM data_penduduk 
                 WHERE tgl_lahir IS NOT NULL AND tgl_lahir != '0000-00-00'
                 GROUP BY MONTH(tgl_lahir)";
    $stmtBulan = $pdo->query($sqlBulan);
    
    // Isi dataBulan dengan hasil query
    while ($row = $stmtBulan->fetch(PDO::FETCH_ASSOC)) {
        $bln = (int)$row['bulan'];
        if ($bln >= 1 && $bln <= 12) {
            $dataBulan[$bln] = (int)$row['total'];
        }
    }

    // Ubah array asosiatif ke array numerik agar sesuai format Highcharts
    $finalBulanData = array_values($dataBulan);

    echo json_encode([
        'status' => 'success',
        'data' => $finalBulanData
    ]);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>