<?php require 'koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Data Penduduk</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
	    
	<!-- Masukkan ini di dalam <head> -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  
    
    <style>
        body { background-color: #f8f9fa; }
        .card { box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: none; margin-bottom: 20px; }
        .highcharts-figure, .highcharts-data-table table { min-width: 310px; max-width: 800px; margin: 1em auto; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark bg-primary mb-4">
    <div class="container">
        <span class="navbar-brand mb-0 h1">Dashboard Kependudukan</span>
    </div>
</nav>

<div class="container">
    <!-- Row: Import & Preview -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-white fw-bold">Import Data Excel</div>
                <div class="card-body">
                    <form id="formImport" enctype="multipart/form-data">
                        <div class="input-group mb-3">
                            <input type="file" class="form-control" id="file_excel" name="file_excel" accept=".xlsx, .xls" required>
                            <button class="btn btn-outline-secondary" type="button" id="btnPreview">Preview</button>
                            <button class="btn btn-primary" type="submit" id="btnImport" disabled>Import Database</button>
                        </div>
                        <small class="text-muted">Pastikan format kolom Excel urut: Kelurahan, KK, NIK, Nama, Tempat Lahir, Tgl Lahir, Perkawinan, Gender, Alamat, RT, Disabilitas.</small>
                    </form>

                    <!-- Area Preview -->
                    <div id="previewArea" class="mt-4 d-none">
                        <h5>Preview Data (10 Baris Pertama)</h5>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered table-hover" id="tablePreview">
                                <thead class="table-light">
                                    <tr>
                                        <th>Kelurahan</th><th>KK</th><th>NIK</th><th>Nama</th><th>Gender</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <div class="alert alert-info mt-2">
                            Ini hanya tampilan data sementara. Klik tombol <b>Import Database</b> untuk menyimpan permanen.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row: Charts -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div id="chartGender" style="height: 300px;"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div id="chartKelurahan" style="height: 300px;"></div>
                </div>
            </div>
        </div>
    </div>
	
<!-- Grafik perbandingan laki perempuan : per kelurahan --> 
<nav class="navbar navbar-dark bg-primary mb-4">
    <div class="container">
        <span class="navbar-brand mb-0 h1">Dashboard Kependudukan</span>
        <div class="btn-group">
            <a href="grafik-perkelurahan.php" class="btn btn-warning fw-bold"><i class="bi bi-gender-ambiguous"></i> Gender</a>
            <a href="grafik-perbulan.php" class="btn btn-success fw-bold"><i class="bi bi-calendar-event"></i> Bulan</a>
            <a href="grafik-tempat-lahir.php" class="btn btn-info fw-bold text-white"><i class="bi bi-geo-alt-fill"></i> Tempat Lahir</a>
            <!-- Tombol Baru -->
            <a href="grafik-perkawinan.php" class="btn btn-danger fw-bold"><i class="bi bi-heart-fill"></i> Perkawinan</a>
        </div>
    </div>
</nav>

 
    <!-- Row: DataTables -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center">
                    <span>Data Penduduk Tersimpan</span>
                    <button class="btn btn-sm btn-success" onclick="location.reload()"><i class="bi bi-arrow-clockwise"></i> Refresh</button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="mainTable" class="table table-striped w-100">
							<thead>
								<tr>
									<th>NIK</th>
									<th>Nama</th>
									<th>Gender</th>
									<th>Tgl Lahir</th>
									<th>Kelurahan</th>
									<th>Disabilitas</th>
									<th width="100">Aksi</th> <!-- Kolom Baru -->
								</tr>
							</thead>
							<tbody>
								<?php
								$stmt = $pdo->query("SELECT * FROM data_penduduk ORDER BY id DESC LIMIT 500");
								while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
									echo "<tr>
										<td>{$row['nik']}</td>
										<td>{$row['nama']}</td>
										<td>{$row['gender']}</td>
										<td>{$row['tgl_lahir']}</td>
										<td>{$row['kelurahan']}</td>
										<td>{$row['disabilitas']}</td>
										<td>
											<!-- Perhatikan penggunaan kutip satu (') pada class dan data-id -->
											
											<!-- Tombol Edit -->
											<button class='btn btn-sm btn-warning btn-edit text-white' data-id='{$row['id']}'>
												<i class='bi bi-pencil-square'></i>
											</button>
											
											<!-- Tombol Hapus -->
											<button class='btn btn-sm btn-danger btn-delete' data-id='{$row['id']}'>
												<i class='bi bi-trash'></i>
											</button>
										</td>
									</tr>";
								}
								?>
							</tbody>
						</table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // 1. Init DataTables
    $('#mainTable').DataTable();

    // 2. Load Charts
    loadCharts();

    // 3. Logic Preview
    $('#btnPreview').click(function() {
        let formData = new FormData($('#formImport')[0]);
        if($('#file_excel').val() == '') {
            Swal.fire('Error', 'Pilih file excel terlebih dahulu', 'error');
            return;
        }

        $.ajax({
            url: 'proses.php?action=preview',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function(){
                Swal.fire({title: 'Memproses...', didOpen: () => { Swal.showLoading() }});
            },
            success: function(response) {
                Swal.close();
                if(response.status == 'success') {
                    let rows = '';
                    response.data.forEach(item => {
                        // Tampilkan hanya 5 kolom utama untuk preview agar rapi
                        rows += `<tr>
                            <td>${item[0] || '-'}</td>
                            <td>${item[1] || '-'}</td>
                            <td>${item[2] || '-'}</td>
                            <td>${item[3] || '-'}</td>
                            <td>${item[7] || '-'}</td>
                        </tr>`;
                    });
                    $('#tablePreview tbody').html(rows);
                    $('#previewArea').removeClass('d-none');
                    $('#btnImport').prop('disabled', false);
                } else {
                    Swal.fire('Gagal', response.message, 'error');
                }
            }
        });
    });

    // 4. Logic Import
    $('#formImport').on('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this);

        Swal.fire({
            title: 'Konfirmasi Import?',
            text: "Data akan dimasukkan ke database.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Import!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'proses.php?action=import',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    beforeSend: function(){
                        Swal.fire({title: 'Sedang Mengimport...', html: 'Mohon tunggu, jangan tutup halaman ini.', allowOutsideClick: false, didOpen: () => { Swal.showLoading() }});
                    },
                    success: function(response) {
                        if(response.status == 'success') {
                            Swal.fire('Berhasil!', response.message, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Gagal', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Terjadi kesalahan server', 'error');
                    }
                });
            }
        });
    });

    // Function Highcharts
    function loadCharts() {
        $.getJSON('proses.php?action=chart_data', function(resp) {
            
            // Format Data Gender Pie Chart
            let genderData = resp.gender.map(item => ({
                name: item.gender == 'L' ? 'Laki-laki' : 'Perempuan',
                y: parseInt(item.total)
            }));

            // Format Data Kelurahan Bar Chart
            let kelCategories = resp.kelurahan.map(item => item.kelurahan);
            let kelData = resp.kelurahan.map(item => parseInt(item.total));

            // Render Chart Gender
            Highcharts.chart('chartGender', {
                chart: { type: 'pie' },
                title: { text: 'Sebaran Gender' },
                series: [{ name: 'Total', data: genderData }]
            });

            // Render Chart Kelurahan
            Highcharts.chart('chartKelurahan', {
                chart: { type: 'column' },
                title: { text: 'Penduduk per Kelurahan' },
                xAxis: { categories: kelCategories },
                yAxis: { title: { text: 'Jumlah' } },
                series: [{ name: 'Penduduk', data: kelData, color: '#28a745' }]
            });
        });
    }
});

    // --- 5. KLIK TOMBOL EDIT (Buka Modal & Isi Data) ---
    $('#mainTable').on('click', '.btn-edit', function() {
        let id = $(this).data('id');
        
        $.ajax({
            url: 'proses.php?action=get_detail',
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function(data) {
                // Isi form modal dengan data dari database
                $('#edit_id').val(data.id);
                $('#edit_nik').val(data.nik);
                $('#edit_kk').val(data.kk);
                $('#edit_nama').val(data.nama);
                $('#edit_kelurahan').val(data.kelurahan);
                $('#edit_tempat_lahir').val(data.tempat_lahir);
                $('#edit_tgl_lahir').val(data.tgl_lahir);
                $('#edit_gender').val(data.gender);
                $('#edit_perkawinan').val(data.perkawinan);
                $('#edit_rt').val(data.rt);
                $('#edit_disabilitas').val(data.disabilitas);
                $('#edit_alamat').val(data.alamat);

                // Tampilkan Modal
                $('#modalEdit').modal('show');
            }
        });
    });

    // --- 6. PROSES SIMPAN EDIT (Form Submit) ---
    $('#formEdit').on('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this);

        $.ajax({
            url: 'proses.php?action=update',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                // Parse JSON response manual jika perlu, atau otomatis dari jQuery
                // (tergantung konfigurasi, jika response plain text gunakan JSON.parse)
                let res = (typeof response === 'object') ? response : JSON.parse(response);

                if(res.status == 'success') {
                    $('#modalEdit').modal('hide');
                    Swal.fire('Berhasil', res.message, 'success').then(() => {
                        location.reload(); // Refresh halaman untuk lihat hasil
                    });
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            }
        });
    });

    // --- 7. KLIK TOMBOL HAPUS ---
    $('#mainTable').on('click', '.btn-delete', function() {
        let id = $(this).data('id');

        Swal.fire({
            title: 'Yakin hapus data ini?',
            text: "Data tidak bisa dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'proses.php?action=delete',
                    type: 'POST',
                    data: { id: id },
                    dataType: 'json',
                    success: function(response) {
                        if(response.status == 'success') {
                            Swal.fire('Terhapus!', response.message, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Gagal', response.message, 'error');
                        }
                    }
                });
            }
        });
    });
</script>
<!-- Modal Edit Data -->
<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title fw-bold">Edit Data Penduduk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEdit">
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id"> <!-- ID Tersembunyi -->
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">NIK</label>
                            <input type="text" class="form-control" name="nik" id="edit_nik" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No. KK</label>
                            <input type="text" class="form-control" name="kk" id="edit_kk">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" name="nama" id="edit_nama" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kelurahan</label>
                            <input type="text" class="form-control" name="kelurahan" id="edit_kelurahan">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Tempat Lahir</label>
                            <input type="text" class="form-control" name="tempat_lahir" id="edit_tempat_lahir">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tgl Lahir</label>
                            <input type="date" class="form-control" name="tgl_lahir" id="edit_tgl_lahir">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Gender</label>
                            <select class="form-select" name="gender" id="edit_gender">
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Perkawinan</label>
                            <input type="text" class="form-control" name="perkawinan" id="edit_perkawinan">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">RT</label>
                            <input type="text" class="form-control" name="rt" id="edit_rt">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Disabilitas</label>
                            <input type="text" class="form-control" name="disabilitas" id="edit_disabilitas">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea class="form-control" name="alamat" id="edit_alamat" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>