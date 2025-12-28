<?php
require '../koneksi.php';
header('Content-Type: application/json');
error_reporting(0);

try {
    // Query: Kelompokkan berdasarkan status perkawinan, hitung L dan P terpisah
    $sql = "SELECT 
                UPPER(perkawinan) as status,
                SUM(CASE WHEN gender = 'L' THEN 1 ELSE 0 END) as total_l,
                SUM(CASE WHEN gender = 'P' THEN 1 ELSE 0 END) as total_p
            FROM data_penduduk 
            WHERE perkawinan IS NOT NULL AND perkawinan != ''
            GROUP BY status
            ORDER BY status ASC";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $categories = [];
    $dataL = [];
    $dataP = [];

    // Variabel bantu untuk menghitung kesimpulan khusus "KAWIN"
    $statsKawin = [
        'L' => 0,
        'P' => 0
    ];

    foreach ($results as $row) {
        $categories[] = $row['status'];
        $valL = (int) $row['total_l'];
        $valP = (int) $row['total_p'];

        $dataL[] = $valL;
        $dataP[] = $valP;

        // Cek jika statusnya mengandung kata "KAWIN" (bukan "BELUM KAWIN")
        // Logika sederhana: Jika string == "KAWIN" atau "CERAI..." dianggap pernah menikah
        if ($row['status'] == 'KAWIN' || strpos($row['status'], 'CERAI') !== false) {
            $statsKawin['L'] += $valL;
            $statsKawin['P'] += $valP;
        }
    }

    echo json_encode([
        'status' => 'success',
        'categories' => $categories,
        'series' => [
            ['name' => 'Laki-laki', 'data' => $dataL, 'color' => '#0d6efd'], // Biru
            ['name' => 'Perempuan', 'data' => $dataP, 'color' => '#d63384']  // Pink
        ],
        'analisa' => $statsKawin // Mengirim data tambahan untuk kesimpulan teks
    ]);

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>