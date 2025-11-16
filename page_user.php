<?php
require 'koneksi.php';

$hasil = [];
$error = '';
$siswa_data = null;

// kalau form disubmit
if (isset($_POST['cek'])) {
    $nama = trim($_POST['nama']);
    $kelas = trim($_POST['kelas']);

    // cari siswa berdasarkan nama & kelas
    $q = mysqli_query($conn, "SELECT * FROM siswa WHERE nama='$nama' AND kelas='$kelas'");
    $siswa_data = mysqli_fetch_assoc($q);

    if (!$siswa_data) {
        $error = "Siswa tidak ditemukan! Pastikan nama & kelas sesuai.";
    } else {
        $id = $siswa_data['id_siswa'];

        // ambil pelanggarannya
        $hasil = mysqli_query($conn, "
            SELECT r.*, p.nama_pelanggaran, p.kategori, pt.nama_petugas 
            FROM riwayat_pelanggaran r
            JOIN pelanggaran p ON r.id_pelanggaran = p.id_pelanggaran
            JOIN petugas pt ON r.id_petugas = pt.id_petugas
            WHERE r.id_siswa = $id
            ORDER BY r.tanggal DESC
        ");
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>User Pelanggaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">

    <h2 class="text-center mb-4">Cek Riwayat Pelanggaran</h2>

    <!-- FORM INPUT NAMA & KELAS -->
    <form method="POST" class="card p-3 mb-4" style="max-width:500px; margin:auto;">
        <h5 class="text-center">Masukkan Identitas</h5>

        <label class="mt-2">Nama Siswa</label>
        <input type="text" name="nama" class="form-control" required>

        <label class="mt-2">Kelas</label>
        <input type="text" name="kelas" class="form-control" required>

        <button type="submit" name="cek" class="btn btn-primary mt-3 w-100">Cek Riwayat</button>
    </form>

    <!-- ERROR -->
    <?php if ($error): ?>
        <div class="alert alert-danger text-center"><?= $error ?></div>
    <?php endif; ?>

    <!-- IDENTITAS SISWA -->
    <?php if ($siswa_data): ?>
        <div class="card p-3 mb-3">
            <h5>Identitas Siswa</h5>
            <p><strong>Nama :</strong> <?= $siswa_data['nama'] ?></p>
            <p><strong>Kelas :</strong> <?= $siswa_data['kelas'] ?></p>
        </div>
    <?php endif; ?>

    <!-- TABEL RIWAYAT -->
    <?php if ($siswa_data): ?>
        <div class="card p-3">
            <h5>Riwayat Pelanggaran</h5>

            <?php if (mysqli_num_rows($hasil) == 0): ?>
                <div class="alert alert-info mt-2">Tidak ada riwayat pelanggaran.</div>
            <?php else: ?>
                <table class="table table-bordered mt-2">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Pelanggaran</th>
                            <th>Kategori</th>
                            <th>Petugas</th>
                            <th>Tanggal</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; while ($r = mysqli_fetch_assoc($hasil)) : ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $r['nama_pelanggaran'] ?></td>
                                <td><?= $r['kategori'] ?></td>
                                <td><?= $r['nama_petugas'] ?></td>
                                <td><?= $r['tanggal'] ?></td>
                                <td><?= $r['keterangan'] ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    <?php endif; ?>

</div>

</body>
</html>
