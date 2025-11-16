<?php

require __DIR__.'/../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Koneksi Database
$koneksi = mysqli_connect("localhost", "root", "123", "db_sukas");

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

// Buat Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Judul Header
$headers = [
    'A1' => 'No',
    'B1' => 'Nama Siswa',
    'C1' => 'Kelas',
    'D1' => 'Pelanggaran',
    'E1' => 'Petugas',
    'F1' => 'Tanggal',
    'G1' => 'Keterangan'
];

foreach ($headers as $cell => $text) {
    $sheet->setCellValue($cell, $text);
}

// Isi Data
$row = 2;
$no = 1;

while ($d = mysqli_fetch_assoc($q)) {
    $sheet->setCellValue('A' . $row, $no++);
    $sheet->setCellValue('B' . $row, $d['nama_siswa']);
    $sheet->setCellValue('C' . $row, $d['kelas']);
    $sheet->setCellValue('D' . $row, $d['nama_pelanggaran']);
    $sheet->setCellValue('E' . $row, $d['nama_petugas']);
    $sheet->setCellValue('F' . $row, $d['tanggal']);
    $sheet->setCellValue('G' . $row, $d['keterangan']);
    $row++;
}

// Auto-size kolom
foreach (range('A', 'G') as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
}

// Nama file
$filename = "riwayat_pelanggaran_" . date('Ymd_His') . ".xlsx";

// Header download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=$filename");
header('Cache-Control: max-age=0');

// Tulis file
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
