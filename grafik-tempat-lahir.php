<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistik Tempat Lahir</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <style>
        body { background-color: #f8f9fa; }
        .card { box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: none; }
        #chartTempatLahir { min-height: 600px; } /* Lebih tinggi agar muat banyak bar */
    </style>
</head>
<body>

<nav class="navbar navbar-dark bg-primary mb-4">
    <div class="container">
        <span class="navbar-brand mb-0 h1">Visualisasi Tempat Lahir</span>
        <div>
            <a href="index.php" class="btn btn-light btn-sm me-1"><i class="bi bi-arrow-left"></i> Dashboard</a>
            <a href="grafik.php" class="btn btn-outline-light btn-sm me-1">Gender</a>
            <a href="grafik_bulan.php" class="btn btn-outline-light btn-sm">Bulan Lahir</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Top 10 Kota Tempat Lahir Penduduk</h5>
                    <button class="btn btn-sm btn-outline-primary" onclick="loadChartTempatLahir()">
                        <i class="bi bi-arrow-clockwise"></i> Refresh
                    </button>
                </div>
                <div class="card-body">
                    <div id="chartTempatLahir"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>

<script>
$(document).ready(function() {
    loadChartTempatLahir();
});

function loadChartTempatLahir() {
    const chartDiv = document.getElementById('chartTempatLahir');
    chartDiv.innerHTML = '<div class="text-center mt-5"><div class="spinner-border text-primary"></div><p>Memuat data...</p></div>';

    $.ajax({
        url: 'api/data-grafik-tempat-lahir.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if(response.status === 'success') {
                Highcharts.chart('chartTempatLahir', {
                    chart: { 
                        type: 'bar' // Bar horizontal
                    },
                    title: { text: 'Distribusi Tempat Lahir (Top 10)' },
                    subtitle: { text: 'Kota/Kabupaten dengan jumlah kelahiran terbanyak' },
                    xAxis: {
                        categories: response.categories,
                        title: { text: null }
                    },
                    yAxis: {
                        min: 0,
                        title: { text: 'Jumlah Penduduk', align: 'high' },
                        labels: { overflow: 'justify' }
                    },
                    tooltip: {
                        valueSuffix: ' Jiwa'
                    },
                    plotOptions: {
                        bar: {
                            dataLabels: {
                                enabled: true
                            },
                            colorByPoint: true // Agar warna tiap bar berbeda-beda
                        }
                    },
                    legend: {
                        enabled: false // Sembunyikan legenda karena kategori sudah jelas di sumbu X
                    },
                    series: [{
                        name: 'Jumlah',
                        data: response.data
                    }],
                    credits: { enabled: false }
                });
            } else {
                chartDiv.innerHTML = '<div class="alert alert-danger">Error: ' + response.message + '</div>';
            }
        },
        error: function(xhr) {
            console.error(xhr);
            chartDiv.innerHTML = '<div class="alert alert-danger">Gagal koneksi ke server.</div>';
        }
    });
}
</script>

</body>
</html>