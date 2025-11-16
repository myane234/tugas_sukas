<?php
// ====== KONEKSI DATABASE ======
require '../koneksi.php';
$no = 1; // untuk nomor urut di tabel


// ====== TAMBAH DATA ======
if (isset($_POST['tambah'])) {
    // handle possible new siswa/pelanggaran/petugas inputs
    $id_siswa = isset($_POST['id_siswa']) ? $_POST['id_siswa'] : '';
    $id_pelanggaran = isset($_POST['id_pelanggaran']) ? $_POST['id_pelanggaran'] : '';
    $id_petugas = isset($_POST['id_petugas']) ? $_POST['id_petugas'] : '';
    $new_siswa_nama = isset($_POST['new_siswa_nama']) ? trim($_POST['new_siswa_nama']) : '';
    $new_siswa_kelas = isset($_POST['new_siswa_kelas']) ? trim($_POST['new_siswa_kelas']) : '';
    $new_pelanggaran_nama = isset($_POST['new_pelanggaran_nama']) ? trim($_POST['new_pelanggaran_nama']) : '';
    $new_pelanggaran_kategori = isset($_POST['new_pelanggaran_kategori']) ? trim($_POST['new_pelanggaran_kategori']) : '';
    $new_petugas_nama = isset($_POST['new_petugas_nama']) ? trim($_POST['new_petugas_nama']) : '';
    $tanggal = $_POST['tanggal'];
    $keterangan = isset($_POST['keterangan']) ? $_POST['keterangan'] : '';

    // Insert new siswa if provided
    if ($new_siswa_nama !== '') {
        $nsn = mysqli_real_escape_string($conn, $new_siswa_nama);
        $nsk = mysqli_real_escape_string($conn, $new_siswa_kelas);
        mysqli_query($conn, "INSERT INTO siswa (nama, kelas) VALUES ('{$nsn}', '{$nsk}')");
        $id_siswa = mysqli_insert_id($conn);
    }

    // Insert new pelanggaran if provided
    if ($new_pelanggaran_nama !== '') {
        $npn = mysqli_real_escape_string($conn, $new_pelanggaran_nama);
        $npk = mysqli_real_escape_string($conn, $new_pelanggaran_kategori);
        mysqli_query($conn, "INSERT INTO pelanggaran (nama_pelanggaran, kategori) VALUES ('{$npn}', '{$npk}')");
        $id_pelanggaran = mysqli_insert_id($conn);
    }

    // Insert new petugas if provided
    if ($new_petugas_nama !== '') {
        $npt = mysqli_real_escape_string($conn, $new_petugas_nama);
        mysqli_query($conn, "INSERT INTO petugas (nama_petugas) VALUES ('{$npt}')");
        $id_petugas = mysqli_insert_id($conn);
    }

    $query = "INSERT INTO riwayat_pelanggaran (id_siswa, id_pelanggaran, id_petugas, tanggal, keterangan)
              VALUES ('$id_siswa', '$id_pelanggaran', '$id_petugas', '$tanggal', '$keterangan')";
    mysqli_query($conn, $query);
    header("Location: index.php");
    exit;
}

// ====== AMBIL SATU RECORD UNTUK EDIT ======
$edit_record = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $res = mysqli_query($conn, "SELECT * FROM riwayat_pelanggaran WHERE id_riwayat='{$edit_id}'");
    $edit_record = mysqli_fetch_assoc($res);
}

// ====== HAPUS DATA ======
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM riwayat_pelanggaran WHERE id_riwayat='$id'");
    header("Location: index.php");
    exit;
}

// ====== UPDATE DATA ======
if (isset($_POST['update'])) {
    // allow same inline-new handling as tambah
    $id_riwayat = $_POST['id_riwayat'];
    $id_siswa = isset($_POST['id_siswa']) ? $_POST['id_siswa'] : '';
    $id_pelanggaran = isset($_POST['id_pelanggaran']) ? $_POST['id_pelanggaran'] : '';
    $id_petugas = isset($_POST['id_petugas']) ? $_POST['id_petugas'] : '';
    $new_siswa_nama = isset($_POST['new_siswa_nama']) ? trim($_POST['new_siswa_nama']) : '';
    $new_siswa_kelas = isset($_POST['new_siswa_kelas']) ? trim($_POST['new_siswa_kelas']) : '';
    $new_pelanggaran_nama = isset($_POST['new_pelanggaran_nama']) ? trim($_POST['new_pelanggaran_nama']) : '';
    $new_pelanggaran_kategori = isset($_POST['new_pelanggaran_kategori']) ? trim($_POST['new_pelanggaran_kategori']) : '';
    $new_petugas_nama = isset($_POST['new_petugas_nama']) ? trim($_POST['new_petugas_nama']) : '';
    $tanggal = $_POST['tanggal'];
    $keterangan = isset($_POST['keterangan']) ? $_POST['keterangan'] : '';

    // same inserts as in tambah
    if ($new_siswa_nama !== '') {
        $nsn = mysqli_real_escape_string($conn, $new_siswa_nama);
        $nsk = mysqli_real_escape_string($conn, $new_siswa_kelas);
        mysqli_query($conn, "INSERT INTO siswa (nama, kelas) VALUES ('{$nsn}', '{$nsk}')");
        $id_siswa = mysqli_insert_id($conn);
    }
    if ($new_pelanggaran_nama !== '') {
        $npn = mysqli_real_escape_string($conn, $new_pelanggaran_nama);
        $npk = mysqli_real_escape_string($conn, $new_pelanggaran_kategori);
        mysqli_query($conn, "INSERT INTO pelanggaran (nama_pelanggaran, kategori) VALUES ('{$npn}', '{$npk}')");
        $id_pelanggaran = mysqli_insert_id($conn);
    }
    if ($new_petugas_nama !== '') {
        $npt = mysqli_real_escape_string($conn, $new_petugas_nama);
        mysqli_query($conn, "INSERT INTO petugas (nama_petugas) VALUES ('{$npt}')");
        $id_petugas = mysqli_insert_id($conn);
    }

    $query = "UPDATE riwayat_pelanggaran SET 
              id_siswa='$id_siswa', id_pelanggaran='$id_pelanggaran', id_petugas='$id_petugas', 
              tanggal='$tanggal', keterangan='$keterangan'
              WHERE id_riwayat='$id_riwayat'";
    mysqli_query($conn, $query);
    header("Location: index.php");
    exit;
}

// ====== AMBIL DATA UNTUK TAMPILAN ======
$siswa = mysqli_query($conn, "SELECT * FROM siswa");
$pelanggaran = mysqli_query($conn, "SELECT * FROM pelanggaran");
$petugas = mysqli_query($conn, "SELECT * FROM petugas");

// Search / filter / grouping via GET
$filter_name = isset($_GET['filter_name']) ? trim($_GET['filter_name']) : '';
$filter_date = isset($_GET['filter_date']) ? trim($_GET['filter_date']) : '';
$filter_petugas = isset($_GET['filter_petugas']) ? trim($_GET['filter_petugas']) : '';
$group_by = isset($_GET['group_by']) ? $_GET['group_by'] : '';

// Build base where clauses
$where = [];
if ($filter_name !== '') {
    $fn = mysqli_real_escape_string($conn, $filter_name);
    $where[] = "s.nama LIKE '%{$fn}%'";
}
if ($filter_date !== '') {
    $fd = mysqli_real_escape_string($conn, $filter_date);
    $where[] = "r.tanggal = '{$fd}'";
}
if ($filter_petugas !== '') {
    $fp = intval($filter_petugas);
    $where[] = "r.id_petugas = {$fp}";
}

$where_sql = '';
if (count($where) > 0) $where_sql = 'WHERE ' . implode(' AND ', $where);

// If grouping requested, return grouped counts
if ($group_by === 'nama') {
    $data = mysqli_query($conn, "SELECT s.nama AS label, COUNT(*) AS cnt
                                 FROM riwayat_pelanggaran r
                                 JOIN siswa s ON r.id_siswa = s.id_siswa
                                 {$where_sql}
                                 GROUP BY s.nama");
    $is_grouped = true;
} elseif ($group_by === 'tanggal') {
    $data = mysqli_query($conn, "SELECT r.tanggal AS label, COUNT(*) AS cnt
                                 FROM riwayat_pelanggaran r
                                 JOIN siswa s ON r.id_siswa = s.id_siswa
                                 {$where_sql}
                                 GROUP BY r.tanggal");
    $is_grouped = true;
} elseif ($group_by === 'petugas') {
    $data = mysqli_query($conn, "SELECT pt.nama_petugas AS label, COUNT(*) AS cnt
                                 FROM riwayat_pelanggaran r
                                 JOIN petugas pt ON r.id_petugas = pt.id_petugas
                                 {$where_sql}
                                 GROUP BY pt.nama_petugas");
    $is_grouped = true;
} else {
    // normal detailed rows
    $data = mysqli_query($conn, "SELECT r.*, s.nama AS nama_siswa, p.nama_pelanggaran, pt.nama_petugas
                                 FROM riwayat_pelanggaran r
                                 JOIN siswa s ON r.id_siswa = s.id_siswa
                                 JOIN pelanggaran p ON r.id_pelanggaran = p.id_pelanggaran
                                 JOIN petugas pt ON r.id_petugas = pt.id_petugas
                                 {$where_sql}
                                 ORDER BY r.id_riwayat ASC");
    $is_grouped = false;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>CRUD Pelanggaran Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">
    <h2 class="text-center mb-4">CRUD Pelanggaran Siswa</h2>
    <a href="../logout.php">Logout</a>

    <!-- Form Tambah / Update -->
    <form method="POST" class="card p-3 mb-4">
        <h5>Input / Edit Pelanggaran</h5>
        <input type="hidden" name="id_riwayat" id="id_riwayat" value="<?= $edit_record ? $edit_record['id_riwayat'] : '' ?>">

        <div class="row g-2">
            <div class="col-md-3">
                <div class="d-flex">
                    <select name="id_siswa" class="form-select" id="select_siswa">
                        <option value="">-- Siswa --</option>
                        <?php foreach ($siswa as $sw): ?>
                            <option value="<?= $sw['id_siswa'] ?>" <?= ($edit_record && $edit_record['id_siswa']==$sw['id_siswa'])? 'selected' : '' ?>><?= $sw['nama'] ?> (<?= $sw['kelas'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                    <button type="button" class="btn btn-outline-secondary ms-2" id="btn_new_siswa">+</button> 
                </div>
            </div>

<div class="col-md-3">
    <div class="d-flex">
        <select name="id_pelanggaran" class="form-select" id="select_pelanggaran">
            <option value="">-- Pelanggaran --</option>
            <?php foreach ($pelanggaran as $pl): ?>
                <option value="<?= $pl['id_pelanggaran'] ?>" <?= ($edit_record && $edit_record['id_pelanggaran']==$pl['id_pelanggaran'])? 'selected' : '' ?>><?= $pl['nama_pelanggaran'] ?> (<?= $pl['kategori'] ?>)</option>
            <?php endforeach; ?>
        </select>
        <button type="button" class="btn btn-outline-secondary ms-2" id="btn_new_pelanggaran">+</button>
    </div>
</div>

            <div class="col-md-3">
                <div class="d-flex">
                    <select name="id_petugas" class="form-select" id="select_petugas">
                        <option value="">-- Petugas --</option>
                        <?php foreach ($petugas as $pt): ?>
                            <option value="<?= $pt['id_petugas'] ?>" <?= ($edit_record && $edit_record['id_petugas']==$pt['id_petugas'])? 'selected' : '' ?>><?= $pt['nama_petugas'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="button" class="btn btn-outline-secondary ms-2" id="btn_new_petugas">+</button>
                </div>
                <div class="mt-2" id="new_petugas_fields" style="display:none;">
                    <input type="text" name="new_petugas_nama" class="form-control" placeholder="Nama petugas">
                </div>
            </div>

            <div class="col-md-3">
                <input type="date" name="tanggal" class="form-control" required value="<?= $edit_record ? $edit_record['tanggal'] : '' ?>">
            </div>
        </div>

        <div class="mt-2">
            <textarea name="keterangan" class="form-control" placeholder="Keterangan tambahan..."></textarea>
        </div>

        <div class="mt-3">
            <button type="submit" name="tambah" class="btn btn-success">Tambah</button>
            <button type="submit" name="update" class="btn btn-primary">Update</button>
        </div>
    </form>

    <!-- Search / Group Controls -->
    <form method="GET" class="card p-3 mb-4">
        <div class="row g-2 align-items-center">
            <div class="col-md-4">
                <input type="text" name="filter_name" class="form-control" placeholder="Cari nama siswa..." value="<?= htmlspecialchars($filter_name) ?>">
            </div>
            <div class="col-md-3">
                <input type="date" name="filter_date" class="form-control" value="<?= htmlspecialchars($filter_date) ?>">
            </div>
            <div class="col-md-3">
                <select name="filter_petugas" class="form-select">
                    <option value="">-- Semua Petugas --</option>
                    <?php foreach ($petugas as $pt): ?>
                        <option value="<?= $pt['id_petugas'] ?>" <?= ($filter_petugas==$pt['id_petugas'])? 'selected' : '' ?>><?= $pt['nama_petugas'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="group_by" class="form-select">
                    <option value="">-- Tampilkan detail --</option>
                    <option value="nama" <?= ($group_by=='nama')? 'selected' : '' ?>>Group by Nama</option>
                    <option value="tanggal" <?= ($group_by=='tanggal')? 'selected' : '' ?>>Group by Tanggal</option>
                    <option value="petugas" <?= ($group_by=='petugas')? 'selected' : '' ?>>Group by Petugas</option>
                </select>
            </div>
        </div>
        <div class="mt-2">
            <button type="submit" class="btn btn-primary">Cari / Group</button>
            <a href="index.php" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    <script>
        // small JS to toggle new-entry fields
        document.getElementById('btn_new_siswa').addEventListener('click', function(){
            var f = document.getElementById('new_siswa_fields');
            if (f.style.display === 'none') f.style.display = 'block'; else f.style.display = 'none';
        });
        document.getElementById('btn_new_pelanggaran').addEventListener('click', function(){
            var f = document.getElementById('new_pelanggaran_fields');
            if (f.style.display === 'none') f.style.display = 'block'; else f.style.display = 'none';
        });
        document.getElementById('btn_new_petugas').addEventListener('click', function(){
            var f = document.getElementById('new_petugas_fields');
            if (f.style.display === 'none') f.style.display = 'block'; else f.style.display = 'none';
        });
    </script>

    <!-- Tabel Data -->
    <table class="table table-bordered table-striped">
        <thead class="table-dark text-center">
            <tr>
                <?php if ($is_grouped): ?>
                    <th>No</th>
                    <th>Label</th>
                    <th>Count</th>
                <?php else: ?>
                    <th>No</th>
                    <th>Nama Siswa</th>
                    <th>Pelanggaran</th>
                    <th>Petugas</th>
                    <th>Tanggal</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
        <?php if ($is_grouped):
            $no = 1;
            while ($d = mysqli_fetch_assoc($data)) : ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td><?= htmlspecialchars($d['label']) ?></td>
                    <td class="text-center"><?= $d['cnt'] ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <?php while ($d = mysqli_fetch_assoc($data)) : ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $d['nama_siswa'] ?></td>
                    <td><?= $d['nama_pelanggaran'] ?></td>
                    <td><?= $d['nama_petugas'] ?></td>
                    <td><?= $d['tanggal'] ?></td>
                    <td><?= $d['keterangan'] ?></td>
                    <td class="text-center">
                        <a href="?edit=<?= $d['id_riwayat'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="?hapus=<?= $d['id_riwayat'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus data ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php endif; ?>
        </tbody>
    </table>
    <button><a href="../reports/report.php" class="btn btn-succes">download</a></button>
</div>

</body>
</html>
