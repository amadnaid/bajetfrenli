<?php
require 'config.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Surabaya Life On a Budget</title>
<link rel="stylesheet" href="style.css">
<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🌴</text></svg>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;0,900;1,400&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>

<body>

<!-- HEADER -->
<header>
  <a href="#home" class="logo">
    <div class="logo-icon">🌴</div>
    <div class="logo-text">
      Surabaya On a Budget
      <span>Hidup Hemat di Kota Pahlawan</span>
    </div>
  </a>

  <nav id="nav">
    <a href="#home">Home</a>
    <a href="#budget">Budget Guide</a>
    <a href="#about">About Us</a>
  </nav>

  <div class="auth-menu">
    <?php if (!isset($_SESSION['user_id']) && !isset($_SESSION['seller_id'])): ?>
      <a href="login.php" class="login-btn">Login</a>
    <?php else: ?>
      <?php
        $profileUrl = isset($_SESSION['user_id']) ? 'profile_user.php' : 'profile_seller.php';
        $namaHalo   = htmlspecialchars($_SESSION['nama'] ?? $_SESSION['nama_usaha'] ?? '');
      ?>
      <a href="<?= $profileUrl ?>" class="user-name" style="text-decoration:none;">
        Halo, <?= $namaHalo ?>
      </a>
      <a href="<?= $profileUrl ?>" class="login-btn" style="margin-right:.4rem;">Profil</a>
      <a href="logout.php" class="login-btn">Logout</a>
    <?php endif; ?>
  </div>

  <div class="hamburger" onclick="toggleNav()" aria-label="Menu">
    <span></span><span></span><span></span>
  </div>
</header>

<!-- HOME -->
<section id="home">
  <div class="home-hero">
    <h1>Hidup Hemat di<br><em>Surabaya</em></h1>
    <p>Panduan lengkap untuk mahasiswa perantauan — nikmati kuliah, jalan-jalan, dan nongki tanpa khawatir kantong jebol.</p>
  </div>

  <div class="carousel-wrapper">
    <div class="carousel" id="carousel">
      <div class="carousel-card">
        <img src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?w=700&q=80" alt="Mahasiswa">
        <div class="carousel-card-body">
          <div class="carousel-card-tag">Tantangan</div>
          <h3>Mahasiswa &amp; Tantangan Biaya Hidup di Surabaya</h3>
        </div>
      </div>
      <div class="carousel-card">
        <img src="https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?w=700&q=80" alt="Hemat">
        <div class="carousel-card-body">
          <div class="carousel-card-tag">Tips</div>
          <h3>Cara Hidup Hemat Sebagai Mahasiswa</h3>
        </div>
      </div>
      <div class="carousel-card">
        <img src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=700&q=80" alt="Budget">
        <div class="carousel-card-body">
          <div class="carousel-card-tag">Panduan</div>
          <h3>Panduan Budget Mahasiswa Di Surabaya</h3>
        </div>
      </div>
    </div>
  </div>

  <div class="home-desc">
    <div class="home-label"><h2>Why Do We Exist?</h2></div>
    <div class="home-desc-inner">
      <p>Surabaya merupakan salah satu kota pendidikan besar di Indonesia. Banyak mahasiswa dari berbagai daerah datang untuk menempuh pendidikan di kampus seperti Institut Teknologi Sepuluh Nopember. Namun, biaya hidup di kota besar sering menjadi tantangan bagi mahasiswa, terutama bagi mahasiswa perantauan.</p>
    </div>
  </div>
</section>

<!-- BUDGET -->
<section id="budget">
  <div class="section-header">
    <div class="section-label">Panduan</div>
    <h2>Budget Guide</h2>
  </div>

  <?php
  // Konfigurasi ikon & deskripsi per kategori (sesuaikan nama_kategori di DB)
  $kategoriConfig = [
    'Warung on a Budget'    => ['icon' => '🍜', 'color' => 'linear-gradient(135deg,#7c4a4a,#5a2d10)', 'desc' => 'Pilihan warung murah meriah dengan menu mengenyangkan dan harga ramah di kantong & cocok untuk makan sehari-hari tanpa bikin budget jebol'],
    'Nongki on a Budget'    => ['icon' => '☕', 'color' => 'linear-gradient(135deg,#7c4a2a,#5a2d10)', 'desc' => 'Rekomendasi cafe nyaman dengan harga terjangkau untuk nongkrong, nugas, & atau sekadar cari suasana santai tanpa harus keluar banyak biaya'],
    'Nongki Non-stop'       => ['icon' => '🍽️', 'color' => 'linear-gradient(135deg,#4a5a7c,#2d3d5a)', 'desc' => 'Tempat makan dan nongkrong yang buka hingga larut malam bahkan 24 jam, & cocok untuk mahasiswa, pekerja malam, atau siapa saja yang butuh tempat fleksibel kapan saja'],
    'Healing Murmer'        => ['icon' => '🌴', 'color' => 'linear-gradient(135deg,#5f8f6b,#3e6b4d)', 'desc' => 'Pilihan tempat wisata menarik dengan biaya masuk terjangkau, & cocok untuk refreshing tanpa menguras dompet'],
    'Fashion Cewek on Budget' => ['icon' => '👗', 'color' => 'linear-gradient(135deg,#b86fa1,#8e4f7a)', 'desc' => 'Rekomendasi tempat belanja fashion wanita dengan model & kekinian dan harga bersahabat, pas untuk tampil stylish dengan budget hemat'],
    'Fashion Cowok on Budget' => ['icon' => '👕', 'color' => 'linear-gradient(135deg,#5a78c9,#3d5ea8)', 'desc' => 'Pilihan store fashion pria dengan koleksi trendi, casual, & hingga formal yang tetap ramah di kantong. Cocok untuk upgrade penampilan tanpa boros'],
  ];

  $kategori = mysqli_query($conn, "SELECT * FROM categories ORDER BY category_id");
  ?>

  <div class="budget-grid">
    <?php while ($row = mysqli_fetch_assoc($kategori)):
      $nama   = $row['nama_kategori'];
      $cfg    = $kategoriConfig[$nama] ?? ['icon' => '📌', 'color' => 'linear-gradient(135deg,#888,#555)', 'desc' => 'Klik untuk melihat rekomendasi ' . $nama];
    ?>
    <div class="budget-card" onclick="openModal(<?= $row['category_id'] ?>)">
      <div class="budget-card-top" style="background:<?= $cfg['color'] ?>">
        <span class="budget-card-icon"><?= $cfg['icon'] ?></span>
      </div>
      <div class="budget-card-body">
        <h3><?= htmlspecialchars($nama) ?></h3>
        <p><?= htmlspecialchars($cfg['desc']) ?></p>
        <button class="btn-explore" onclick="event.stopPropagation(); openModal(<?= $row['category_id'] ?>)">
          Explore →
        </button>
      </div>
    </div>
    <?php endwhile; ?>
  </div>

  <!-- Tombol Review & Daftar Usaha -->
  <div class="action-section-wrapper">
    <div class="section-label">Bergabung</div>
    <h2 class="action-section-title">Kontribusi & Daftarkan Usahamu</h2>
    <p class="action-section-sub">Bantu sesama mahasiswa dengan berbagi pengalaman, atau daftarkan usahamu agar lebih dikenal!</p>
    <div class="budget-action-section">
      <?php if (!isset($_SESSION['user_id']) && !isset($_SESSION['seller_id'])): ?>
        <a href="login.php?message=user" class="budget-action-btn">✍️ Tulis Reviewmu!</a>
        <a href="login.php?message=seller" class="budget-action-btn seller-btn">🏪 Daftarkan Usahamu!</a>

      <?php elseif (isset($_SESSION['user_id'])): ?>
        <a href="#budget" class="budget-action-btn" onclick="openWriteReview()">✍️ Tulis Reviewmu!</a>
        <button class="budget-action-btn seller-btn" onclick="sellerRequired()">🏪 Daftarkan Usahamu!</button>

      <?php elseif (isset($_SESSION['seller_id'])): ?>
        <button class="budget-action-btn" onclick="alert('Login sebagai pengguna untuk memberi review.')">✍️ Tulis Reviewmu!</button>
        <a href="register_business.php" class="budget-action-btn seller-btn">🏪 Daftarkan Usahamu!</a>
      <?php endif; ?>
    </div>
  </div>

  <!-- BUDGET KALKULATOR -->
  <div class="budget-tool">
    <h3>Cek Rekomendasi Budget-mu</h3>
    <p>Masukkan budget kamu (Rp) dan lihat usaha apa saja yang bisa dijangkau!</p>
    <div class="budget-input-row">
      <input type="number" id="budget-input" placeholder="Masukkan nominal (Rp)" min="0">
      <button onclick="generateBudget()">Cek Rekomendasi</button>
    </div>
    <div id="result-grid"></div>
  </div>

  <!-- REALITY CHECK -->
  <div class="section-header" style="padding-top:10px;">
    <div class="section-label">Reality Check</div>
    <h2>Realita Harga</h2>
  </div>

  <div class="reality-grid">
    <div class="reality-card">
      <h4>🌿 Wisata</h4>
      <ul>
        <li>Taman Grand Harvest <span class="price-tag">GRATIS</span></li>
        <li>Taman Angsa <span class="price-tag">GRATIS</span></li>
        <li>Graha Natura Park <span class="price-tag">GRATIS</span></li>
      </ul>
    </div>
    <div class="reality-card">
      <h4>☕ Nongki</h4>
      <ul>
        <li>Warkop Goeboeg 99 <span class="price-tag">1k – 25k</span></li>
        <li>Kedai Kaladulu <span class="price-tag">15k – 30k</span></li>
        <li>Rene Cafe <span class="price-tag">20k – 30k</span></li>
      </ul>
    </div>
    <div class="reality-card">
      <h4>👗 Fashion</h4>
      <ul>
        <li>Dafarin Production <span class="price-tag">50k+</span></li>
        <li>Rubylicious <span class="price-tag">50k – 150k</span></li>
        <li>TCO <span class="price-tag">50k – 150k</span></li>
      </ul>
    </div>
    <div class="reality-card">
      <h4>💡 Solusi Hemat</h4>
      <ul>
        <li>Catat pengeluaran harian <span class="price-tag">✔</span></li>
        <li>Tentukan budget tiap pos <span class="price-tag">✔</span></li>
        <li>Sisihkan tabungan duluan <span class="price-tag">✔</span></li>
      </ul>
    </div>
  </div>

  <!-- TIPS -->
  <div class="section-header" style="padding-top:10px;">
    <div class="section-label">Strategi</div>
    <h2>Tips Hidup Hemat</h2>
  </div>

  <div class="tips-section">
    <div class="tips-row">
      <div class="tip-card"><span class="tip-icon">🍳</span><h5>Masak Sendiri</h5><p>Masak di kos bisa hemat hingga 50% dibanding makan di luar setiap hari.</p></div>
      <div class="tip-card"><span class="tip-icon">🎓</span><h5>Promo Mahasiswa</h5><p>Manfaatkan diskon KTM dan promo khusus pelajar di berbagai tempat.</p></div>
      <div class="tip-card"><span class="tip-icon">🚌</span><h5>Transport Umum</h5><p>Gunakan angkutan umum atau sepeda untuk hemat ongkos transportasi.</p></div>
      <div class="tip-card"><span class="tip-icon">⏰</span><h5>Batasi Nongki</h5><p>Nongki boleh, tapi tentukan frekuensi dan budget yang masuk akal.</p></div>
      <div class="tip-card"><span class="tip-icon">📒</span><h5>Catat Keuangan</h5><p>Gunakan aplikasi atau buku catatan untuk monitor pengeluaran harian.</p></div>
    </div>
  </div>
</section>

<!-- ABOUT -->
<section id="about">
  <div class="about-container">
    <div class="section-label">Tim Kami</div>
    <h2>About Us</h2>
    <div class="about-desc">
      <p>Website <strong>Surabaya Life On a Budget</strong> dibuat untuk membantu mahasiswa menjalani kehidupan hemat di Surabaya. Website ini menyediakan rekomendasi wisata gratis, tempat nongkrong murah, fashion terjangkau, serta panduan pengelolaan budget mahasiswa.</p>
    </div>
    <div class="about-features">
      <div class="about-feature"><span class="about-feature-icon">🌿</span><h5>Wisata Gratis</h5><p>Kurasi taman &amp; destinasi 0 rupiah terbaik di Surabaya</p></div>
      <div class="about-feature"><span class="about-feature-icon">☕</span><h5>Nongki Murah</h5><p>Tempat nongkrong ramah kantong dengan WiFi kencang</p></div>
      <div class="about-feature"><span class="about-feature-icon">👗</span><h5>Fashion Hemat</h5><p>Outfit kekinian tanpa harus menguras tabungan</p></div>
      <div class="about-feature"><span class="about-feature-icon">📊</span><h5>Budget Planner</h5><p>Cek rekomendasi sesuai budget yang kamu punya</p></div>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer>
  <div class="footer-brand">🌴 Surabaya Life On a Budget</div>
  <p>📸 Instagram: <a href="#">@surabayaonbudget</a></p>
  <p>© 2026 Surabaya Life On a Budget. All rights reserved.</p>
</footer>

<!-- MODAL OVERLAY (satu saja) -->
<div class="modal-overlay" id="modal-overlay" onclick="handleOverlayClick(event)">
  <div class="modal-box" id="modal-box">
    <button class="modal-close" onclick="closeModal()">✕</button>
    <div id="modal-content">
      <div class="modal-loading">Memuat...</div>
    </div>
  </div>
</div>

<!-- Notifikasi sukses -->
<?php if (isset($_GET['review_success'])): ?>
<div class="toast" id="toast">✅ Review berhasil dikirim!</div>
<?php elseif (isset($_GET['business_added'])): ?>
<div class="toast" id="toast">🏪 Usaha berhasil didaftarkan!</div>
<?php elseif (isset($_GET['photo_added'])): ?>
<div class="toast" id="toast">📸 Foto berhasil diupload!</div>
<?php endif; ?>

<script>
/* ===== NAV ===== */
function toggleNav() {
  document.getElementById('nav').classList.toggle('open');
}

/* ===== CAROUSEL DRAG ===== */
const carousel = document.getElementById('carousel');
let isDragging = false, startX, scrollLeft;
carousel.addEventListener('mousedown', e => {
  isDragging = true; startX = e.pageX - carousel.offsetLeft; scrollLeft = carousel.scrollLeft;
  carousel.style.cursor = 'grabbing';
});
carousel.addEventListener('mouseleave', () => { isDragging = false; carousel.style.cursor = 'grab'; });
carousel.addEventListener('mouseup',    () => { isDragging = false; carousel.style.cursor = 'grab'; });
carousel.addEventListener('mousemove', e => {
  if (!isDragging) return; e.preventDefault();
  carousel.scrollLeft = scrollLeft - (e.pageX - carousel.offsetLeft - startX) * 1.5;
});

/* ===== MODAL BUKA KATEGORI ===== */
function openModal(categoryId) {
  document.getElementById('modal-content').innerHTML = '<div class="modal-loading">Memuat...</div>';
  document.getElementById('modal-overlay').classList.add('active');
  document.body.style.overflow = 'hidden';

  fetch('get_category.php?id=' + categoryId)
    .then(r => r.text())
    .then(html => {
      document.getElementById('modal-content').innerHTML = html;
    })
    .catch(() => {
      document.getElementById('modal-content').innerHTML = '<p>Gagal memuat data.</p>';
    });
}

/* ===== MODAL BUKA DETAIL TEMPAT ===== */
function openPlace(id) {
  document.getElementById('modal-content').innerHTML = '<div class="modal-loading">Memuat...</div>';

  fetch('get_place.php?id=' + id)
    .then(r => r.text())
    .then(html => {
      document.getElementById('modal-content').innerHTML = html;
    })
    .catch(() => {
      document.getElementById('modal-content').innerHTML = '<p>Gagal memuat data.</p>';
    });
}

/* ===== MODAL TUTUP ===== */
function closeModal() {
  document.getElementById('modal-overlay').classList.remove('active');
  document.body.style.overflow = '';
  document.getElementById('modal-content').innerHTML = '';
}

function handleOverlayClick(e) {
  if (e.target === document.getElementById('modal-overlay')) closeModal();
}

/* ===== WRITE REVIEW: arahkan ke kategori dulu ===== */
function openWriteReview() {
  alert('Pilih tempat di Budget Guide, lalu scroll ke bawah untuk tulis review!');
}

/* ===== SELLER REQUIRED ===== */
function sellerRequired() {
  alert('Fitur ini hanya untuk akun Penjual.\nSilakan login sebagai Penjual terlebih dahulu.');
}

/* ===== BUDGET KALKULATOR ===== */
function generateBudget() {
  const budget = parseInt(document.getElementById('budget-input').value);
  const grid   = document.getElementById('result-grid');

  if (!budget || budget <= 0) {
    grid.innerHTML = '<p class="budget-hint">Masukkan nominal budget yang valid.</p>';
    return;
  }

  grid.innerHTML = '<div class="modal-loading">Mencari rekomendasi...</div>';

  fetch('get_budget_recommendations.php?budget=' + budget)
    .then(r => r.text())
    .then(html => { grid.innerHTML = html; })
    .catch(() => { grid.innerHTML = '<p>Gagal memuat rekomendasi.</p>'; });
}

document.getElementById('budget-input').addEventListener('keydown', e => {
  if (e.key === 'Enter') generateBudget();
});

/* ===== TOAST NOTIFIKASI ===== */
const toast = document.getElementById('toast');
if (toast) {
  toast.classList.add('show');
  setTimeout(() => toast.classList.remove('show'), 3500);

  // Kalau ada ?place=X, buka modal place tersebut
  const params  = new URLSearchParams(window.location.search);
  const placeId = params.get('place');
  if (placeId) {
    setTimeout(() => openPlace(parseInt(placeId)), 400);
  }
}
</script>

</body>
</html>