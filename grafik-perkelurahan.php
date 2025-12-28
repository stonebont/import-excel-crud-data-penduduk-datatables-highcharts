<?php
require 'koneksi.php'; // Pastikan file koneksi di-include

// Query data ringkasan
try {
    $totalWarga = $pdo->query("SELECT COUNT(*) FROM data_penduduk")->fetchColumn();
    $totalL     = $pdo->query("SELECT COUNT(*) FROM data_penduduk WHERE gender='L'")->fetchColumn();
    $totalP     = $pdo->query("SELECT COUNT(*) FROM data_penduduk WHERE gender='P'")->fetchColumn();
} catch (Exception $e) {
    $totalWarga = $totalL = $totalP = 0;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistik Gender per Kelurahan</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <style>
        body { background-color: #f8f9fa; }
        .card { box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: none; }
        #chartContainer { min-height: 500px; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark bg-primary mb-4">
    <div class="container">
        <span class="navbar-brand mb-0 h1">Visualisasi Data Kependudukan</span>
        <a href="index.php" class="btn btn-light btn-sm"><i class="bi bi-arrow-left"></i> Kembali ke Data</a>
    </div>
</nav>

<!-- Row: Summary Cards -->
<div class="row mb-4">
    <!-- Total Penduduk -->
    <div class="col-md-4">
        <div class="card text-white bg-primary mb-3 h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="card-title mb-0">Total Penduduk</h6>
                    <h2 class="fw-bold mb-0"><?= number_format($totalWarga, 0, ',', '.') ?></h2>
                    <small>Jiwa</small>
                </div>
                <i class="bi bi-people-fill" style="font-size: 3rem; opacity: 0.5;"></i>
            </div>
        </div>
    </div>

    <!-- Total Laki-laki -->
    <div class="col-md-4">
        <div class="card text-white bg-success mb-3 h-100"> <!-- Hijau/Biru Tua -->
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="card-title mb-0">Laki-laki</h6>
                    <h2 class="fw-bold mb-0"><?= number_format($totalL, 0, ',', '.') ?></h2>
                    <small>Jiwa</small>
                </div>
                <i class="bi bi-gender-male" style="font-size: 3rem; opacity: 0.5;"></i>
            </div>
        </div>
    </div>

    <!-- Total Perempuan -->
    <div class="col-md-4">
        <div class="card text-white bg-danger mb-3 h-100"> <!-- Merah/Pink -->
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="card-title mb-0">Perempuan</h6>
                    <h2 class="fw-bold mb-0"><?= number_format($totalP, 0, ',', '.') ?></h2>
                    <small>Jiwa</small>
                </div>
                <i class="bi bi-gender-female" style="font-size: 3rem; opacity: 0.5;"></i>
            </div>
        </div>
    </div>
</div>
<!-- End Row Summary -->

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Grafik Persebaran Gender per Kelurahan</h5>
                    <button class="btn btn-sm btn-outline-primary" onclick="loadChart()">
                        <i class="bi bi-arrow-clockwise"></i> Refresh Data
                    </button>
                </div>
                <div class="card-body">
                    <!-- Tempat Grafik Muncul -->
                    <div id="chartContainer"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>

<script>
$(document).ready(function() {
    loadChart();
});

function loadChart() {
    // Tampilkan loading text (opsional)
    const chartDiv = document.getElementById('chartContainer');
    chartDiv.innerHTML = '<div class="text-center mt-5"><div class="spinner-border text-primary" role="status"></div><p>Memuat data...</p></div>';

    $.getJSON('api/data-grafik-perkelurahan.php', function(response) {
        if(response.status === 'success') {
            
            Highcharts.chart('chartContainer', {
                chart: {
                    type: 'column'
                },
                title: {
                    text: 'Perbandingan Laki-laki & Perempuan per Kelurahan',
                    align: 'left'
                },
                subtitle: {
                    text: 'Sumber: Database Kependudukan',
                    align: 'left'
                },
                xAxis: {
                    categories: response.categories,
                    crosshair: true,
                    title: {
                        text: 'Kelurahan'
                    }
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Jumlah Penduduk (Jiwa)'
                    }
                },
                tooltip: {
                    headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                    pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                        '<td style="padding:0"><b>{point.y}</b></td></tr>',
                    footerFormat: '</table>',
                    shared: true,
                    useHTML: true
                },
                plotOptions: {
                    column: {
                        pointPadding: 0.2,
                        borderWidth: 0,
                        borderRadius: 3
                    }
                },
                series: response.series,
                credits: {
                    enabled: false
                }
            });

        } else {
            chartDiv.innerHTML = '<div class="alert alert-danger">Gagal memuat data chart.</div>';
        }
    }).fail(function() {
        chartDiv.innerHTML = '<div class="alert alert-danger">Terjadi kesalahan koneksi server.</div>';
    });
}
</script>

</body>
</html>