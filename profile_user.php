<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?message=user");
    exit;
}

$user_id = (int)$_SESSION['user_id'];

// Ambil data user terbaru dari DB
$userQuery = mysqli_query($conn, "SELECT * FROM users WHERE user_id = $user_id");
$user = mysqli_fetch_assoc($userQuery);

// Ambil riwayat review user ini (join dengan businesses)
$reviewQuery = mysqli_query($conn,
    "SELECT ratings.*, businesses.nama_tempat, businesses.business_id, categories.nama_kategori
     FROM ratings
     JOIN businesses ON businesses.business_id = ratings.business_id
     JOIN categories ON categories.category_id = businesses.category_id
     WHERE ratings.user_id = $user_id
     ORDER BY ratings.created_at DESC"
);

$totalReview = mysqli_num_rows($reviewQuery);

// Hitung rata-rata rating yang pernah user ini berikan
$avgQuery = mysqli_query($conn, "SELECT AVG(rating) as avg FROM ratings WHERE user_id = $user_id");
$avgRow   = mysqli_fetch_assoc($avgQuery);
$avgGiven = round($avgRow['avg'] ?? 0, 1);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profil – <?= htmlspecialchars($user['nama']) ?></title>
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
    <span class="user-name">Halo, <?= htmlspecialchars($user['nama']) ?></span>
    <a href="logout.php" class="login-btn">Logout</a>
  </div>
</header>

<main class="profile-main">

  <!-- SIDEBAR PROFIL -->
  <aside class="profile-sidebar">

    <div class="avatar-wrap">
      <?php if ($user['foto_profil']): ?>
        <img src="uploads/avatars/<?= htmlspecialchars($user['foto_profil']) ?>"
             alt="Foto Profil" class="avatar-img" id="avatarPreview">
      <?php else: ?>
        <div class="avatar-placeholder" id="avatarPreview">
          <?= strtoupper(mb_substr($user['nama'], 0, 1)) ?>
        </div>
      <?php endif; ?>
      <label class="avatar-edit-btn" for="avatarInput" title="Ganti foto">✏️</label>
    </div>

    <h2 class="profile-name"><?= htmlspecialchars($user['nama']) ?></h2>
    <p class="profile-email"><?= htmlspecialchars($user['email']) ?></p>
    <p class="profile-since">
      Bergabung sejak <?= date('M Y', strtotime($user['tanggal_daftar'])) ?>
    </p>

    <div class="profile-stats">
      <div class="stat-item">
        <span class="stat-number"><?= $totalReview ?></span>
        <span class="stat-label">Review</span>
      </div>
      <div class="stat-item">
        <span class="stat-number"><?= $avgGiven > 0 ? $avgGiven : '–' ?></span>
        <span class="stat-label">Avg Rating</span>
      </div>
    </div>

    <button class="btn-edit-profile" onclick="toggleEditForm()">✏️ Edit Profil</button>
    <a href="index.php" class="btn-back">← Kembali ke Beranda</a>

  </aside>

  <!-- KONTEN UTAMA -->
  <div class="profile-content">

    <!-- FORM EDIT PROFIL (tersembunyi default) -->
    <div class="edit-form-card" id="editFormCard" style="display:none;">
      <h3>Edit Profil</h3>

      <?php if (isset($_GET['updated'])): ?>
        <div class="alert-success">✅ Profil berhasil diperbarui!</div>
      <?php endif; ?>
      <?php if (isset($_GET['error'])): ?>
        <div class="alert-error">❌ <?= htmlspecialchars($_GET['error']) ?></div>
      <?php endif; ?>

      <form action="update_profile_user.php" method="POST" enctype="multipart/form-data" class="edit-form">

        <div class="form-group">
          <label>Foto Profil</label>
          <input type="file" name="foto_profil" id="avatarInput" accept="image/*"
                 onchange="previewAvatar(this)">
        </div>

        <div class="form-group">
          <label>Nama Lengkap</label>
          <input type="text" name="nama" value="<?= htmlspecialchars($user['nama']) ?>" required>
        </div>

        <div class="form-group">
          <label>Email</label>
          <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
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
          <label>Password Saat Ini <small>(wajib untuk menyimpan perubahan)</small></label>
          <input type="password" name="password_lama" placeholder="Password saat ini..." required>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn-save">Simpan Perubahan</button>
          <button type="button" class="btn-cancel" onclick="toggleEditForm()">Batal</button>
        </div>

      </form>
    </div>

    <!-- RIWAYAT REVIEW -->
    <div class="section-card">
      <h3>Riwayat Review <span class="badge"><?= $totalReview ?></span></h3>

      <?php if ($totalReview === 0): ?>
        <div class="empty-state">
          <span>📝</span>
          <p>Kamu belum pernah menulis review.</p>
          <a href="index.php#budget" class="btn-explore">Jelajahi Tempat →</a>
        </div>

      <?php else: ?>
        <div class="review-history-list">

        <?php
        // Reset pointer
        mysqli_data_seek($reviewQuery, 0);
        while ($rev = mysqli_fetch_assoc($reviewQuery)):
        ?>
          <div class="review-history-card"
               onclick="goToReview(<?= $rev['business_id'] ?>)"
               title="Klik untuk melihat di halaman utama">

            <div class="review-history-meta">
              <span class="review-place-name">
                🏪 <?= htmlspecialchars($rev['nama_tempat']) ?>
              </span>
              <span class="review-category-tag">
                <?= htmlspecialchars($rev['nama_kategori']) ?>
              </span>
            </div>

            <div class="review-stars">
              <?= str_repeat('★', (int)$rev['rating']) ?>
              <?= str_repeat('☆', 5 - (int)$rev['rating']) ?>
              <span class="review-score"><?= $rev['rating'] ?>/5</span>
            </div>

            <p class="review-komentar">
              "<?= htmlspecialchars($rev['komentar']) ?>"
            </p>

            <small class="review-date">
              <?= date('d F Y, H:i', strtotime($rev['created_at'])) ?>
            </small>

          </div>
        <?php endwhile; ?>

        </div>
      <?php endif; ?>
    </div>

  </div>
</main>

<!-- MODAL (untuk openPlace dari riwayat) -->
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

function previewAvatar(input) {
  if (!input.files || !input.files[0]) return;
  const reader = new FileReader();
  reader.onload = e => {
    const prev = document.getElementById('avatarPreview');
    if (prev.tagName === 'IMG') {
      prev.src = e.target.result;
    } else {
      // Ganti div placeholder jadi img
      const img = document.createElement('img');
      img.src = e.target.result;
      img.className = 'avatar-img';
      img.id = 'avatarPreview';
      prev.replaceWith(img);
    }
  };
  reader.readAsDataURL(input.files[0]);
}

function goToReview(businessId) {
  document.getElementById('modal-content').innerHTML = '<div class="modal-loading">Memuat...</div>';
  document.getElementById('modal-overlay').classList.add('active');
  document.body.style.overflow = 'hidden';
  fetch('get_place.php?id=' + businessId)
    .then(r => r.text())
    .then(html => {
      document.getElementById('modal-content').innerHTML = html;
      // Scroll ke bagian review dalam modal
      setTimeout(() => {
        const reviewSection = document.querySelector('#modal-content .review-list');
        if (reviewSection) reviewSection.scrollIntoView({ behavior: 'smooth' });
      }, 200);
    });
}

function openPlace(id) {
  goToReview(id);
}

function closeModal() {
  document.getElementById('modal-overlay').classList.remove('active');
  document.body.style.overflow = '';
  document.getElementById('modal-content').innerHTML = '';
}

function handleOverlayClick(e) {
  if (e.target === document.getElementById('modal-overlay')) closeModal();
}

// Buka edit form otomatis kalau ada parameter error/updated
<?php if (isset($_GET['updated']) || isset($_GET['error'])): ?>
toggleEditForm();
window.scrollTo({ top: 0, behavior: 'smooth' });
<?php endif; ?>
</script>

</body>
</html>