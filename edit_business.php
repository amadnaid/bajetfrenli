<?php
require 'config.php';

if (!isset($_SESSION['seller_id'])) {
    header("Location: login.php?message=seller");
    exit;
}

$seller_id = (int)$_SESSION['seller_id'];

// Ambil usaha milik seller ini
$businessQuery = mysqli_query($conn,
    "SELECT * FROM businesses WHERE seller_id = $seller_id LIMIT 1"
);
$business = mysqli_fetch_assoc($businessQuery);

if (!$business) {
    header("Location: register_business.php");
    exit;
}

$categoryQuery = mysqli_query($conn, "SELECT * FROM categories ORDER BY category_id");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Usaha – <?= htmlspecialchars($business['nama_tempat']) ?></title>
<link rel="stylesheet" href="register_business.css">
<link rel="stylesheet" href="profile.css">
</head>
<body>

<div class="business-container">

  <div class="business-header">
    <h1>Edit Usahamu</h1>
    <p>Perbarui informasi usaha kamu agar pengunjung mendapat info terkini.</p>
  </div>

  <?php if (isset($_GET['updated'])): ?>
    <div class="alert-success" style="margin-bottom:1rem;">✅ Usaha berhasil diperbarui!</div>
  <?php endif; ?>
  <?php if (isset($_GET['error'])): ?>
    <div class="alert-error" style="margin-bottom:1rem;">❌ <?= htmlspecialchars($_GET['error']) ?></div>
  <?php endif; ?>

  <form action="update_business.php" method="POST" enctype="multipart/form-data" class="business-form">
    <input type="hidden" name="business_id" value="<?= $business['business_id'] ?>">

    <div class="form-group">
      <label>Nama Tempat</label>
      <input type="text" name="nama_tempat"
             value="<?= htmlspecialchars($business['nama_tempat']) ?>" required>
    </div>

    <div class="form-group">
      <label>Kategori</label>
      <select name="category_id" required>
        <?php while ($cat = mysqli_fetch_assoc($categoryQuery)): ?>
          <option value="<?= $cat['category_id'] ?>"
            <?= $cat['category_id'] == $business['category_id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($cat['nama_kategori']) ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>

    <div class="form-group">
      <label>Deskripsi</label>
      <textarea name="deskripsi" rows="5" required><?= htmlspecialchars($business['deskripsi']) ?></textarea>
    </div>

    <div class="form-group">
      <label>Alamat</label>
      <textarea name="alamat" rows="3" required><?= htmlspecialchars($business['alamat']) ?></textarea>
    </div>

    <div class="double-input">
      <div class="form-group">
        <label>Harga Minimum</label>
        <input type="number" name="harga_min" value="<?= $business['harga_min'] ?>">
      </div>
      <div class="form-group">
        <label>Harga Maksimum</label>
        <input type="number" name="harga_max" value="<?= $business['harga_max'] ?>">
      </div>
    </div>

    <div class="form-group">
      <label>Jam Operasional</label>
      <input type="text" name="jam_buka"
             value="<?= htmlspecialchars($business['jam_buka'] ?? '') ?>"
             placeholder="08.00 – 22.00">
    </div>

    <div class="form-group">
      <label>Kontak</label>
      <input type="text" name="kontak"
             value="<?= htmlspecialchars($business['kontak'] ?? '') ?>">
    </div>

    <div class="form-group">
      <label>Ganti Foto Utama <small>(kosongkan jika tidak ingin ganti)</small></label>
      <?php if ($business['foto_utama']): ?>
        <img src="uploads/businesses/<?= htmlspecialchars($business['foto_utama']) ?>"
             style="max-width:200px; border-radius:8px; margin-bottom:.5rem; display:block;"
             onerror="this.style.display='none'">
      <?php endif; ?>
      <input type="file" name="foto_utama" accept="image/*">
    </div>

    <div class="form-actions" style="display:flex; gap:1rem;">
      <button type="submit" class="submit-btn">Simpan Perubahan</button>
      <a href="profile_seller.php" class="submit-btn"
         style="background:#888; text-decoration:none; text-align:center;">Batal</a>
    </div>
  </form>

</div>
</body>
</html>