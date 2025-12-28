<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistik Kelahiran per Bulan</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <style>
        body { background-color: #f8f9fa; }
        .card { box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: none; }
        #chartBulanLahir { min-height: 500px; } /* Tinggi yang cukup untuk grafik */
    </style>
</head>
<body>

<nav class="navbar navbar-dark bg-primary mb-4">
    <div class="container">
        <span class="navbar-brand mb-0 h1">Visualisasi Kelahiran</span>
        <a href="index.php" class="btn btn-light btn-sm me-2"><i class="bi bi-arrow-left"></i> Kembali ke Dashboard</a>
        <a href="grafik-perkelurahan.php" class="btn btn-outline-light btn-sm"><i class="bi bi-gender-ambiguous"></i> Lihat Grafik Gender</a>
    </div>
</nav>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Tren Kelahiran Warga Berdasarkan Bulan</h5>
                    <button class="btn btn-sm btn-outline-primary" onclick="loadChartBulanLahir()">
                        <i class="bi bi-arrow-clockwise"></i> Refresh Data
                    </button>
                </div>
                <div class="card-body">
                    <!-- Tempat Grafik Bulan Lahir Muncul -->
                    <div id="chartBulanLahir"></div>
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
    loadChartBulanLahir();
});

function loadChartBulanLahir() {
    // Tampilkan loading text
    const chartDiv = document.getElementById('chartBulanLahir');
    chartDiv.innerHTML = '<div class="text-center mt-5"><div class="spinner-border text-primary" role="status"></div><p>Memuat data...</p></div>';

    $.ajax({
        url: 'api/data-grafik-perbulan.php', // Mengambil data dari endpoint khusus
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log("Data bulan lahir diterima:", response); // Debugging

            if(response.status === 'success') {
                Highcharts.chart('chartBulanLahir', {
                    chart: { 
                        type: 'line', // Atau 'area', 'column'
                        zoomType: 'x' // Memungkinkan zoom horizontal
                    },
                    title: { text: 'Statistik Kelahiran Warga per Bulan' },
                    subtitle: { text: 'Klik dan tarik untuk zoom, double-klik untuk reset zoom.' },
                    xAxis: {
                        categories: [
                            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                        ],
                        crosshair: true,
                        title: { text: 'Bulan' }
                    },
                    yAxis: {
                        min: 0,
                        title: { text: 'Jumlah Kelahiran (Jiwa)' }
                    },
                    tooltip: {
                        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                            '<td style="padding:0"><b>{point.y} Orang</b></td></tr>',
                        footerFormat: '</table>',
                        shared: true,
                        useHTML: true
                    },
                    plotOptions: {
                        line: {
                            dataLabels: {
                                enabled: true, // Menampilkan angka di atas titik garis
                                format: '{y}'
                            },
                            enableMouseTracking: true,
                            marker: {
                                enabled: true // Tampilkan titik-titik data
                            }
                        }
                    },
                    series: [{
                        name: 'Jumlah Warga Lahir',
                        data: response.data,
                        color: '#fd7e14' // Warna Orange
                    }],
                    credits: { enabled: false }
                });

            } else {
                chartDiv.innerHTML = '<div class="alert alert-danger">Gagal memuat data: ' + response.message + '</div>';
            }
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error:", error);
            console.log("Response Text:", xhr.responseText);
            chartDiv.innerHTML = '<div class="alert alert-danger">Terjadi kesalahan saat mengambil data. Cek konsol browser (F12) untuk detail.</div>';
        }
    });
}
</script>

</body>
</html>