<?php
require 'vendor/autoload.php';
require 'koneksi.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

// --- 1. PREVIEW DATA EXCEL ---
if ($action == 'preview') {
    if (isset($_FILES['file_excel']['name'])) {
        $file_tmp = $_FILES['file_excel']['tmp_name'];
        $reader = IOFactory::createReaderForFile($file_tmp);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($file_tmp);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        // Hapus header (baris pertama) jika perlu
        array_shift($rows); 
        
        // Batasi preview 10 baris saja agar ringan
        $previewData = array_slice($rows, 0, 10); 
        
        echo json_encode(['status' => 'success', 'data' => $previewData]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'File tidak ditemukan']);
    }
}

// --- 2. IMPORT DATA KE DATABASE ---
elseif ($action == 'import') {
    if (isset($_FILES['file_excel']['name'])) {
        try {
            $file_tmp = $_FILES['file_excel']['tmp_name'];
            $reader = IOFactory::createReaderForFile($file_tmp);
            $spreadsheet = $reader->load($file_tmp);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();
            
            // Hapus header
            array_shift($rows);

            $sql = "INSERT INTO data_penduduk (kelurahan, kk, nik, nama, tempat_lahir, tgl_lahir, perkawinan, gender, alamat, rt, disabilitas) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE nama=VALUES(nama)";
            $stmt = $pdo->prepare($sql);

            $count = 0;
            foreach ($rows as $row) {
                // Pastikan baris tidak kosong
                if(empty($row[2])) continue; // Skip jika NIK kosong

                // Konversi Tanggal Excel ke Y-m-d
                $tgl_lahir = NULL;
                if (!empty($row[5])) {
                    if (is_numeric($row[5])) {
                        $tgl_lahir = Date::excelToDateTimeObject($row[5])->format('Y-m-d');
                    } else {
                        $tgl_lahir = date('Y-m-d', strtotime($row[5]));
                    }
                }

                $stmt->execute([
                    $row[0], // Kelurahan
                    $row[1], // KK
                    $row[2], // NIK
                    $row[3], // Nama
                    $row[4], // Tempat Lahir
                    $tgl_lahir,
                    $row[6], // Perkawinan
                    $row[7], // Gender (L/P)
                    $row[8], // Alamat
                    $row[9], // RT
                    $row[10] // Disabilitas
                ]);
                $count++;
            }
            echo json_encode(['status' => 'success', 'message' => "$count Data berhasil diimport!"]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}

// --- 3. AMBIL DATA UNTUK CHART ---
elseif ($action == 'chart_data') {
    // Chart 1: Gender
    $stmtGender = $pdo->query("SELECT gender, COUNT(*) as total FROM data_penduduk GROUP BY gender");
    $dataGender = $stmtGender->fetchAll(PDO::FETCH_ASSOC);

    // Chart 2: Per Kelurahan
    $stmtKel = $pdo->query("SELECT kelurahan, COUNT(*) as total FROM data_penduduk GROUP BY kelurahan");
    $dataKel = $stmtKel->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'gender' => $dataGender,
        'kelurahan' => $dataKel
    ]);
}

// --- 4. AMBIL 1 DATA (Untuk Mengisi Form Edit) ---
elseif ($action == 'get_detail') {
    $id = $_POST['id'] ?? 0;
    $stmt = $pdo->prepare("SELECT * FROM data_penduduk WHERE id = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($data);
}

// --- 5. UPDATE DATA ---
elseif ($action == 'update') {
    try {
        $sql = "UPDATE data_penduduk SET 
                kelurahan=?, kk=?, nik=?, nama=?, tempat_lahir=?, 
                tgl_lahir=?, perkawinan=?, gender=?, alamat=?, rt=?, disabilitas=? 
                WHERE id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $_POST['kelurahan'], $_POST['kk'], $_POST['nik'], $_POST['nama'],
            $_POST['tempat_lahir'], $_POST['tgl_lahir'], $_POST['perkawinan'],
            $_POST['gender'], $_POST['alamat'], $_POST['rt'], 
            $_POST['disabilitas'], $_POST['id']
        ]);
        echo json_encode(['status' => 'success', 'message' => 'Data berhasil diperbarui']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

// --- 6. HAPUS DATA ---
elseif ($action == 'delete') {
    $id = $_POST['id'] ?? 0;
    try {
        $stmt = $pdo->prepare("DELETE FROM data_penduduk WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['status' => 'success', 'message' => 'Data berhasil dihapus']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>

