<?php
// Koneksi Database
$koneksi = mysqli_connect("localhost", "root", "", "db_sukas");

// Query JOIN
$q = mysqli_query($koneksi, "
    SELECT r.id_riwayat, s.nama AS nama_siswa, s.kelas,
           p.nama_pelanggaran, pg.nama_petugas,
           r.tanggal, r.keterangan
    FROM riwayat_pelanggaran r
    LEFT JOIN siswa s ON r.id_siswa = s.id_siswa
    LEFT JOIN pelanggaran p ON r.id_pelanggaran = p.id_pelanggaran
    LEFT JOIN petugas pg ON r.id_petugas = pg.id_petugas
    ORDER BY r.id_riwayat ASC
");

// Nama file
$filename = "riwayat_pelanggaran_" . date('Ymd_His') . ".csv";

// Header biar langsung download
header('Content-Type: text/csv; charset=utf-8');
header("Content-Disposition: attachment; filename=$filename");

// Output buffer ke file
$output = fopen('php://output', 'w');

// Header kolom CSV
fputcsv($output, ['No', 'Nama Siswa', 'Kelas', 'Pelanggaran', 'Petugas', 'Tanggal', 'Keterangan']);

$no = 1;

// Isi data baris per baris
while ($d = mysqli_fetch_assoc($q)) {
    fputcsv($output, [
        $no++,
        $d['nama_siswa'],
        $d['kelas'],
        $d['nama_pelanggaran'],
        $d['nama_petugas'],
        $d['tanggal'],
        $d['keterangan']
    ]);
}

fclose($output);
exit;
?>
