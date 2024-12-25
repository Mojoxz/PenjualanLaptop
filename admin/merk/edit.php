<?php
session_start();
require_once '../../config/koneksi.php';

// Cek login
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

// Cek parameter id
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$merk_id = $_GET['id'];
$merk = query("SELECT * FROM tb_merk WHERE merk_id = $merk_id")[0];

// Proses edit merk
if (isset($_POST['edit'])) {
    $nama_merk = htmlspecialchars($_POST['nama_merk']);
    $deskripsi = htmlspecialchars($_POST['deskripsi']);
    
    // Validasi
    if (empty($nama_merk)) {
        $error = "Nama merk harus diisi!";
    } else {
        // Cek duplikat nama merk, kecuali untuk merk yang sedang diedit
        $cek = query("SELECT * FROM tb_merk WHERE nama_merk = '$nama_merk' AND merk_id != $merk_id");
        
        if ($cek) {
            $error = "Nama merk sudah ada!";
        } else {
            // Update data
            $data = [
                'nama_merk' => $nama_merk,
                'deskripsi' => $deskripsi
            ];
            
            if (ubah('tb_merk', $data, "merk_id = $merk_id")) {
                $_SESSION['success'] = "Merk berhasil diupdate!";
                header("Location: index.php");
                exit;
            } else {
                $error = "Gagal mengupdate merk!";
            }
        }
    }
}

// Include header
include_once '../includes/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Edit Merk</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="../index.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="index.php">Merk</a></li>
        <li class="breadcrumb-item active">Edit Merk</li>
    </ol>

    <div class="row">
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-pencil-square me-1"></i>
                    Form Edit Merk
                </div>
                <div class="card-body">
                    <?php if (isset($error)) : ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form action="" method="post" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="nama_merk" class="form-label">Nama Merk</label>
                            <input type="text" class="form-control" id="nama_merk" name="nama_merk" 
                                   value="<?= $merk['nama_merk']; ?>" required>
                            <div class="invalid-feedback">
                                Nama merk harus diisi!
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"><?= $merk['deskripsi']; ?></textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="index.php" class="btn btn-secondary">Kembali</a>
                            <button type="submit" name="edit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>