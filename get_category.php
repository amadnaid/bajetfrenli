<?php
require 'config.php';

$id = (int)$_GET['id'];

// Ambil nama kategori
$catQuery = mysqli_query($conn, "SELECT nama_kategori FROM categories WHERE category_id = $id");
$cat = mysqli_fetch_assoc($catQuery);
$namaKategori = $cat ? htmlspecialchars($cat['nama_kategori']) : 'Kategori';

// Ikon & warna per kategori
$kategoriConfig = [
    'Warung on a Budget'      => ['icon' => '🍜', 'color' => '#7c4a4a'],
    'Nongki on a Budget'      => ['icon' => '☕', 'color' => '#7c4a2a'],
    'Nongki Non-stop'         => ['icon' => '🍽️', 'color' => '#4a5a7c'],
    'Healing Murmer'          => ['icon' => '🌴', 'color' => '#5f8f6b'],
    'Fashion Cewek on Budget' => ['icon' => '👗', 'color' => '#b86fa1'],
    'Fashion Cowok on Budget' => ['icon' => '👕', 'color' => '#5a78c9'],
];
$cfg = $kategoriConfig[$cat['nama_kategori']] ?? ['icon' => '📌', 'color' => '#888'];

// Ambil semua usaha + rata-rata rating
$query = mysqli_query($conn,
    "SELECT b.*,
            ROUND(AVG(r.rating), 1) AS avg_rating,
            COUNT(r.rating_id)      AS total_review
     FROM businesses b
     LEFT JOIN ratings r ON r.business_id = b.business_id
     WHERE b.category_id = $id
     GROUP BY b.business_id
     ORDER BY b.created_at DESC"
);
?>

<div class="cat-header">
    <div class="cat-header-icon" style="background:<?= $cfg['color'] ?>">
        <?= $cfg['icon'] ?>
    </div>
    <div>
        <h2 class="modal-title"><?= $namaKategori ?></h2>
        <p class="modal-subtitle">Klik kartu untuk lihat detail & review</p>
    </div>
</div>

<div class="category-grid">

<?php if (mysqli_num_rows($query) === 0): ?>
    <div class="empty-state">
        <span>🏪</span>
        <p>Belum ada usaha terdaftar di kategori ini.</p>
    </div>

<?php else: ?>

    <?php while ($row = mysqli_fetch_assoc($query)):
        $avgR  = $row['avg_rating'] ?? 0;
        $total = $row['total_review'] ?? 0;
        $filled = (int)round($avgR);
        $isGratis = ($row['harga_min'] == 0);
    ?>
    <div class="cat-card" onclick="openPlace(<?= $row['business_id'] ?>)">

        <div class="cat-card-img-wrap">
            <?php if ($row['foto_utama']): ?>
                <img src="uploads/businesses/<?= htmlspecialchars($row['foto_utama']) ?>"
                     alt="<?= htmlspecialchars($row['nama_tempat']) ?>"
                     onerror="this.src='https://placehold.co/400x200/e8d5bc/6b3a26?text=No+Image'">
            <?php else: ?>
                <img src="https://placehold.co/400x200/e8d5bc/6b3a26?text=No+Image" alt="">
            <?php endif; ?>
            <div class="cat-card-price-badge <?= $isGratis ? 'gratis' : '' ?>">
                <?php if ($isGratis): ?>
                    GRATIS
                <?php else: ?>
                    Rp<?= number_format($row['harga_min'], 0, ',', '.') ?>
                    <?php if ($row['harga_max'] && $row['harga_max'] != $row['harga_min']): ?>
                        &ndash; <?= number_format($row['harga_max'], 0, ',', '.') ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="cat-card-body">
            <h4><?= htmlspecialchars($row['nama_tempat']) ?></h4>

            <?php if ($row['alamat']): ?>
                <p class="cat-card-addr">
                    📍 <?= htmlspecialchars(mb_substr($row['alamat'], 0, 60)) ?><?= mb_strlen($row['alamat']) > 60 ? '…' : '' ?>
                </p>
            <?php endif; ?>

            <p class="cat-card-desc">
                <?= htmlspecialchars(mb_substr($row['deskripsi'], 0, 90)) ?>…
            </p>

            <div class="cat-card-footer">
                <div class="cat-stars">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <span class="<?= $i <= $filled ? 'star-on' : 'star-off' ?>">★</span>
                    <?php endfor; ?>
                    <span class="cat-review-count">
                        <?= $avgR > 0 ? $avgR . ' · ' : '' ?><?= $total ?> ulasan
                    </span>
                </div>
                <span class="cat-card-cta">Lihat →</span>
            </div>
        </div>

    </div>
    <?php endwhile; ?>

<?php endif; ?>

</div>