<?php
require 'config.php';

if (!isset($_SESSION['seller_id'])) {
    header("Location: login.php?message=seller");
    exit;
}

$seller_id = (int)$_SESSION['seller_id'];

// Data seller
$sellerQuery = mysqli_query($conn, "SELECT * FROM sellers WHERE seller_id = $seller_id");
$seller      = mysqli_fetch_assoc($sellerQuery);

// Usaha milik seller ini (maks 1)
$businessQuery = mysqli_query($conn,
    "SELECT businesses.*, categories.nama_kategori
     FROM businesses
     JOIN categories ON categories.category_id = businesses.category_id
     WHERE businesses.seller_id = $seller_id
     LIMIT 1"
);
$business = mysqli_fetch_assoc($businessQuery);

// Jika punya usaha, ambil statistik review
$totalReview = 0;
$avgRating   = 0;
if ($business) {
    $bid = (int)$business['business_id'];

    $statQuery = mysqli_query($conn,
        "SELECT COUNT(*) as total, AVG(rating) as avg FROM ratings WHERE business_id = $bid"
    );
    $stat        = mysqli_fetch_assoc($statQuery);
    $totalReview = $stat['total'] ?? 0;
    $avgRating   = round($stat['avg'] ?? 0, 1);

    // Review terbaru (5)
    $reviewQuery = mysqli_query($conn,
        "SELECT ratings.*, users.nama
         FROM ratings
         JOIN users ON users.user_id = ratings.user_id
         WHERE ratings.business_id = $bid
         ORDER BY ratings.created_at DESC
         LIMIT 5"
    );
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profil Penjual – <?= htmlspecialchars($seller['nama_usaha']) ?></title>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="profile.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body class="profile-page">

<!-- HEADER -->
<header>
  <a href="index.php" class="logo">
    <div class="logo-icon">🌴</div>
    <div class="logo-text">Surabaya On a Budget<span>Hidup Hemat di Kota Pahlawan</span></div>
  </a>
  <nav id="nav">
    <a href="index.php#home">Home</a>
    <a href="index.php#budget">Budget Guide</a>
    <a href="index.php#about">About Us</a>
  </nav>
  <div class="auth-menu">
    <span class="user-name">Halo, <?= htmlspecialchars($seller['nama_usaha']) ?></span>
    <a href="logout.php" class="login-btn">Logout</a>
  </div>
</header>

<main class="profile-main">

  <!-- SIDEBAR -->
  <aside class="profile-sidebar">
    <div class="avatar-wrap">
      <div class="avatar-placeholder seller-avatar">
        🏪
      </div>
    </div>

    <h2 class="profile-name"><?= htmlspecialchars($seller['nama_usaha']) ?></h2>
    <p class="profile-role-badge">Akun Penjual</p>
    <p class="profile-email"><?= htmlspecialchars($seller['email']) ?></p>
    <p class="profile-since">
      Bergabung sejak <?= date('M Y', strtotime($seller['tanggal_daftar'])) ?>
    </p>

    <div class="profile-stats">
      <div class="stat-item">
        <span class="stat-number"><?= $totalReview ?></span>
        <span class="stat-label">Review</span>
      </div>
      <div class="stat-item">
        <span class="stat-number"><?= $avgRating > 0 ? $avgRating : '–' ?></span>
        <span class="stat-label">Avg Rating</span>
      </div>
    </div>

    <?php
    $statusClass = [
        'Pending'   => 'status-pending',
        'Disetujui' => 'status-approved',
        'Ditolak'   => 'status-rejected',
    ][$seller['status_verifikasi']] ?? 'status-pending';
    ?>
    <div class="seller-status <?= $statusClass ?>">
      Status: <?= htmlspecialchars($seller['status_verifikasi']) ?>
    </div>

    <button class="btn-edit-profile" onclick="toggleEditForm()">✏️ Edit Profil</button>
    <a href="index.php" class="btn-back">← Kembali ke Beranda</a>
  </aside>

  <!-- KONTEN -->
  <div class="profile-content">

    <!-- FORM EDIT PROFIL PENJUAL -->
    <div class="edit-form-card" id="editFormCard" style="display:none;">
      <h3>Edit Profil Penjual</h3>

      <?php if (isset($_GET['updated'])): ?>
        <div class="alert-success">✅ Profil berhasil diperbarui!</div>
      <?php endif; ?>
      <?php if (isset($_GET['error'])): ?>
        <div class="alert-error">❌ <?= htmlspecialchars($_GET['error']) ?></div>
      <?php endif; ?>

      <form action="update_profile_seller.php" method="POST" class="edit-form">

        <div class="form-group">
          <label>Nama Pemilik</label>
          <input type="text" name="nama_pemilik"
                 value="<?= htmlspecialchars($seller['nama_pemilik']) ?>" required>
        </div>

        <div class="form-group">
          <label>Nama Usaha</label>
          <input type="text" name="nama_usaha"
                 value="<?= htmlspecialchars($seller['nama_usaha']) ?>" required>
        </div>

        <div class="form-group">
          <label>Email</label>
          <input type="email" name="email"
                 value="<?= htmlspecialchars($seller['email']) ?>" required>
        </div>

        <div class="form-group">
          <label>Nomor Telepon</label>
          <input type="text" name="nomor_telepon"
                 value="<?= htmlspecialchars($seller['nomor_telepon'] ?? '') ?>">
        </div>

        <div class="form-group">
          <label>Alamat</label>
          <textarea name="alamat" rows="3"><?= htmlspecialchars($seller['alamat'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
          <label>Password Baru <small>(kosongkan jika tidak ingin ganti)</small></label>
          <input type="password" name="password_baru" placeholder="Password baru...">
        </div>

        <div class="form-group">
          <label>Konfirmasi Password Baru</label>
          <input type="password" name="password_konfirm" placeholder="Ulangi password baru...">
        </div>

        <div class="form-group">
          <label>Password Saat Ini <small>(wajib)</small></label>
          <input type="password" name="password_lama" required placeholder="Password saat ini...">
        </div>

        <div class="form-actions">
          <button type="submit" class="btn-save">Simpan Perubahan</button>
          <button type="button" class="btn-cancel" onclick="toggleEditForm()">Batal</button>
        </div>
      </form>
    </div>

    <!-- INFO USAHA -->
    <div class="section-card">
      <div class="section-card-header">
        <h3>Usaha Terdaftar</h3>
        <?php if (!$business): ?>
          <a href="register_business.php" class="btn-explore">+ Daftarkan Usaha</a>
        <?php endif; ?>
      </div>

      <?php if (!$business): ?>
        <div class="empty-state">
          <span>🏪</span>
          <p>Kamu belum mendaftarkan usaha.</p>
          <a href="register_business.php" class="btn-explore">Daftarkan Sekarang →</a>
        </div>

      <?php else: ?>
        <div class="business-profile-card">

          <?php if ($business['foto_utama']): ?>
            <img class="business-cover"
                 src="uploads/businesses/<?= htmlspecialchars($business['foto_utama']) ?>"
                 alt="<?= htmlspecialchars($business['nama_tempat']) ?>"
                 onerror="this.src='https://placehold.co/700x250/e8f5e9/2d6a4f?text=No+Image'">
          <?php else: ?>
            <img class="business-cover"
                 src="https://placehold.co/700x250/e8f5e9/2d6a4f?text=No+Image" alt="">
          <?php endif; ?>

          <div class="business-profile-body">
            <div class="business-profile-top">
              <div>
                <span class="review-category-tag"><?= htmlspecialchars($business['nama_kategori']) ?></span>
                <h4><?= htmlspecialchars($business['nama_tempat']) ?></h4>
              </div>
              <a href="edit_business.php" class="btn-edit-small">✏️ Edit Usaha</a>
            </div>

            <div class="business-profile-info">
              <p>📍 <?= htmlspecialchars($business['alamat']) ?></p>
              <?php if ($business['jam_buka']): ?>
                <p>🕐 <?= htmlspecialchars($business['jam_buka']) ?></p>
              <?php endif; ?>
              <?php if ($business['kontak']): ?>
                <p>📞 <?= htmlspecialchars($business['kontak']) ?></p>
              <?php endif; ?>
              <p class="price-range">
                Rp<?= number_format($business['harga_min'], 0, ',', '.') ?>
                – Rp<?= number_format($business['harga_max'], 0, ',', '.') ?>
              </p>
            </div>

            <p class="business-desc"><?= nl2br(htmlspecialchars($business['deskripsi'])) ?></p>

            <div class="business-rating-summary">
              <span class="stars">
                <?= str_repeat('★', (int)round($avgRating)) ?>
                <?= str_repeat('☆', 5 - (int)round($avgRating)) ?>
              </span>
              <span><?= $avgRating ?>/5 · <?= $totalReview ?> review</span>
            </div>
          </div>
        </div>

        <!-- REVIEW TERBARU -->
        <?php if ($totalReview > 0): ?>
        <div class="recent-reviews">
          <h4>Review Terbaru</h4>
          <?php while ($rev = mysqli_fetch_assoc($reviewQuery)): ?>
            <div class="review-item">
              <strong><?= htmlspecialchars($rev['nama']) ?></strong>
              <div class="stars small">
                <?= str_repeat('★', (int)$rev['rating']) ?>
                <?= str_repeat('☆', 5 - (int)$rev['rating']) ?>
              </div>
              <p><?= htmlspecialchars($rev['komentar']) ?></p>
              <small class="review-date"><?= date('d M Y', strtotime($rev['created_at'])) ?></small>
            </div>
          <?php endwhile; ?>

          <?php if ($totalReview > 5): ?>
            <button class="btn-lihat-semua"
                    onclick="openPlace(<?= $business['business_id'] ?>)">
              Lihat semua <?= $totalReview ?> review →
            </button>
          <?php endif; ?>
        </div>
        <?php endif; ?>

      <?php endif; ?>
    </div>

  </div>
</main>

<!-- MODAL -->
<div class="modal-overlay" id="modal-overlay" onclick="handleOverlayClick(event)">
  <div class="modal-box" id="modal-box">
    <button class="modal-close" onclick="closeModal()">✕</button>
    <div id="modal-content"><div class="modal-loading">Memuat...</div></div>
  </div>
</div>

<script>
function toggleEditForm() {
  const card = document.getElementById('editFormCard');
  card.style.display = card.style.display === 'none' ? 'block' : 'none';
  if (card.style.display === 'block') {
    card.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
}

function openPlace(id) {
  document.getElementById('modal-content').innerHTML = '<div class="modal-loading">Memuat...</div>';
  document.getElementById('modal-overlay').classList.add('active');
  document.body.style.overflow = 'hidden';
  fetch('get_place.php?id=' + id)
    .then(r => r.text())
    .then(html => { document.getElementById('modal-content').innerHTML = html; });
}

function closeModal() {
  document.getElementById('modal-overlay').classList.remove('active');
  document.body.style.overflow = '';
  document.getElementById('modal-content').innerHTML = '';
}

function handleOverlayClick(e) {
  if (e.target === document.getElementById('modal-overlay')) closeModal();
}

<?php if (isset($_GET['updated']) || isset($_GET['error'])): ?>
toggleEditForm();
window.scrollTo({ top: 0, behavior: 'smooth' });
<?php endif; ?>
</script>

</body>
</html>