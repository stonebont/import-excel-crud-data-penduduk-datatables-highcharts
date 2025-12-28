<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analisa Status Perkawinan</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <style>
        body { background-color: #f8f9fa; }
        .card { box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: none; }
        #chartPerkawinan { min-height: 500px; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark bg-primary mb-4">
    <div class="container">
        <span class="navbar-brand mb-0 h1">Analisa Perkawinan & Gender</span>
        <div>
            <a href="index.php" class="btn btn-light btn-sm me-1"><i class="bi bi-house-door-fill"></i> Home</a>
            <a href="grafik-perkelurahan.php" class="btn btn-outline-light btn-sm">Gender</a>
        </div>
    </div>
</nav>

<div class="container">
    
    <!-- Kotak Kesimpulan Otomatis -->
    <div class="row mb-4 d-none" id="summaryRow">
        <div class="col-md-12">
            <div class="alert alert-info border-0 shadow-sm d-flex align-items-center" role="alert">
                <i class="bi bi-lightbulb-fill fs-2 me-3 text-warning"></i>
                <div>
                    <h5 class="alert-heading fw-bold">Hasil Analisa Data:</h5>
                    <p class="mb-0" id="textKesimpulan">Menghitung data...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik -->
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Distribusi Status Perkawinan per Gender</h5>
                    <button class="btn btn-sm btn-outline-primary" onclick="loadChartPerkawinan()">
                        <i class="bi bi-arrow-clockwise"></i> Refresh
                    </button>
                </div>
                <div class="card-body">
                    <div id="chartPerkawinan"></div>
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
    loadChartPerkawinan();
});

function loadChartPerkawinan() {
    const chartDiv = document.getElementById('chartPerkawinan');
    chartDiv.innerHTML = '<div class="text-center mt-5"><div class="spinner-border text-primary"></div><p>Memuat data...</p></div>';

    $.ajax({
        url: 'api/data-grafik-perkawinan.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if(response.status === 'success') {
                
                // 1. Render Chart
                Highcharts.chart('chartPerkawinan', {
                    chart: { type: 'column' },
                    title: { text: 'Perbandingan Status Perkawinan (L vs P)' },
                    xAxis: {
                        categories: response.categories,
                        crosshair: true,
                        title: { text: 'Status Perkawinan' }
                    },
                    yAxis: {
                        min: 0,
                        title: { text: 'Jumlah Penduduk' }
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
                    credits: { enabled: false }
                });

                // 2. Logika Kesimpulan (Teks di atas grafik)
                const stats = response.analisa;
                const totalL = stats.L;
                const totalP = stats.P;
                let kesimpulan = "";

                if (totalP > totalL) {
                    kesimpulan = `Terdapat lebih banyak <b>Perempuan</b> yang berstatus Kawin/Cerai (${totalP} jiwa) dibandingkan <b>Laki-laki</b> (${totalL} jiwa).`;
                } else if (totalL > totalP) {
                    kesimpulan = `Terdapat lebih banyak <b>Laki-laki</b> yang berstatus Kawin/Cerai (${totalL} jiwa) dibandingkan <b>Perempuan</b> (${totalP} jiwa).`;
                } else {
                    kesimpulan = `Jumlah Laki-laki dan Perempuan yang berstatus Kawin/Cerai adalah <b>sama</b> (${totalL} jiwa).`;
                }

                $('#textKesimpulan').html(kesimpulan);
                $('#summaryRow').removeClass('d-none'); // Tampilkan kotak alert

            } else {
                chartDiv.innerHTML = '<div class="alert alert-danger">' + response.message + '</div>';
            }
        },
        error: function(xhr) {
            console.error(xhr);
            chartDiv.innerHTML = '<div class="alert alert-danger">Gagal koneksi server.</div>';
        }
    });
}
</script>

</body>
</html>