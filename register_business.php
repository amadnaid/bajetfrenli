<?php
require 'config.php';

if (!isset($_SESSION['seller_id'])) {
    echo "<script>alert('Silakan login sebagai penjual terlebih dahulu!'); window.location='login.php';</script>";
    exit;
}

$seller_id = (int)$_SESSION['seller_id'];

// Cek apakah seller sudah punya usaha (maks 1)
$cekUsaha = mysqli_query($conn,
    "SELECT business_id FROM businesses WHERE seller_id = $seller_id LIMIT 1"
);
if (mysqli_num_rows($cekUsaha) > 0) {
    // Sudah punya usaha → redirect ke halaman edit
    header("Location: edit_business.php?info=sudah_terdaftar");
    exit;
}

$categoryQuery = mysqli_query($conn, "SELECT * FROM categories");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Daftarkan Usahamu</title>
<link rel="stylesheet" href="register_business.css">
</head>
<body>

<div class="business-container">

  <div class="business-header">
    <h1>Daftarkan Usahamu!</h1>
    <p>Perkenalkan usaha kamu kepada ribuan mahasiswa dan warga Surabaya yang mencari pilihan hemat.</p>
  </div>

  <form action="submit_business.php" method="POST" enctype="multipart/form-data" class="business-form">

    <div class="form-group">
      <label>Nama Tempat</label>
      <input type="text" name="nama_tempat" required>
    </div>

    <div class="form-group">
      <label>Kategori</label>
      <select name="category_id" required>
        <option value="">Pilih Kategori</option>
        <?php while ($cat = mysqli_fetch_assoc($categoryQuery)): ?>
          <option value="<?= $cat['category_id'] ?>">
            <?= htmlspecialchars($cat['nama_kategori']) ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>

    <div class="form-group">
      <label>Deskripsi</label>
      <textarea name="deskripsi" rows="5" required></textarea>
    </div>

    <div class="form-group">
      <label>Alamat</label>
      <textarea name="alamat" rows="3" required></textarea>
    </div>

    <div class="double-input">
      <div class="form-group">
        <label>Harga Minimum</label>
        <input type="number" name="harga_min" min="0">
      </div>
      <div class="form-group">
        <label>Harga Maksimum</label>
        <input type="number" name="harga_max" min="0">
      </div>
    </div>

    <div class="form-group">
      <label>Jam Operasional</label>
      <input type="text" name="jam_buka" placeholder="08.00 – 22.00">
    </div>

    <div class="form-group">
      <label>Kontak</label>
      <input type="text" name="kontak">
    </div>

    <div class="form-group">
      <label>Foto Utama</label>
      <input type="file" name="foto_utama" accept="image/*" required>
    </div>

    <button type="submit" class="submit-btn">Daftarkan Usaha</button>

  </form>
</div>
</body>
</html>