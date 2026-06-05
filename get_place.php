<?php
require 'config.php';

$id = (int)$_GET['id'];

// Data tempat
$placeQuery = mysqli_query($conn, "SELECT * FROM businesses WHERE business_id = $id");
$business = mysqli_fetch_assoc($placeQuery);

if (!$business) {
    echo '<p class="error">Tempat tidak ditemukan.</p>';
    exit;
}

// Rata-rata rating
$ratingQuery = mysqli_query($conn,
    "SELECT AVG(rating) AS avg_rating, COUNT(*) AS total_review
     FROM ratings WHERE business_id = $id"
);
$ratingData  = mysqli_fetch_assoc($ratingQuery);
$avgRating   = round($ratingData['avg_rating'] ?? 0, 1);
$totalReview = $ratingData['total_review'] ?? 0;
$filledStars = (int)round($avgRating);

// Review pengunjung
$reviewQuery = mysqli_query($conn,
    "SELECT ratings.*, users.nama
     FROM ratings
     JOIN users ON users.user_id = ratings.user_id
     WHERE ratings.business_id = $id
     ORDER BY ratings.created_at DESC"
);

// Foto dokumentasi
$photoQuery = mysqli_query($conn,
    "SELECT business_photos.*, users.nama
     FROM business_photos
     JOIN users ON users.user_id = business_photos.user_id
     WHERE business_photos.business_id = $id
     ORDER BY business_photos.uploaded_at DESC"
);

// Cek apakah user sudah review
$sudahReview = false;
if (isset($_SESSION['user_id'])) {
    $cekReview = mysqli_query($conn,
        "SELECT rating_id FROM ratings
         WHERE business_id = $id AND user_id = " . (int)$_SESSION['user_id']
    );
    $sudahReview = mysqli_num_rows($cekReview) > 0;
}

$isGratis = ($business['harga_min'] == 0);
?>

<div class="place-detail">

    <!-- ── HERO FOTO ── -->
    <div class="place-hero">
        <?php if ($business['foto_utama']): ?>
            <img class="place-foto-utama"
                 src="uploads/businesses/<?= htmlspecialchars($business['foto_utama']) ?>"
                 alt="<?= htmlspecialchars($business['nama_tempat']) ?>"
                 onerror="this.src='https://placehold.co/800x340/e8d5bc/6b3a26?text=No+Image'">
        <?php else: ?>
            <img class="place-foto-utama"
                 src="https://placehold.co/800x340/e8d5bc/6b3a26?text=No+Image"
                 alt="No image">
        <?php endif; ?>

        <!-- Rating badge atas foto -->
        <div class="place-hero-rating">
            <span class="place-hero-stars">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <span class="<?= $i <= $filledStars ? 'sh-star-on' : 'sh-star-off' ?>">★</span>
                <?php endfor; ?>
            </span>
            <span><?= $avgRating > 0 ? $avgRating : '–' ?></span>
            <span class="place-hero-rev-count">(<?= $totalReview ?> ulasan)</span>
        </div>
    </div>

    <!-- ── BODY ── -->
    <div class="place-card-body">

        <h3 class="place-name"><?= htmlspecialchars($business['nama_tempat']) ?></h3>

        <!-- Info chips -->
        <div class="place-chips">
            <span class="chip chip-price <?= $isGratis ? 'chip-gratis' : '' ?>">
                💰
                <?php if ($isGratis): ?>
                    GRATIS
                <?php else: ?>
                    Rp<?= number_format($business['harga_min'], 0, ',', '.') ?>
                    <?php if ($business['harga_max'] && $business['harga_max'] != $business['harga_min']): ?>
                        &ndash; Rp<?= number_format($business['harga_max'], 0, ',', '.') ?>
                    <?php endif; ?>
                <?php endif; ?>
            </span>
            <span class="chip">📍 <?= htmlspecialchars($business['alamat']) ?></span>
            <?php if ($business['jam_buka']): ?>
                <span class="chip">🕐 <?= htmlspecialchars($business['jam_buka']) ?></span>
            <?php endif; ?>
            <?php if ($business['kontak']): ?>
                <span class="chip">📞 <?= htmlspecialchars($business['kontak']) ?></span>
            <?php endif; ?>
        </div>

        <p class="place-desc"><?= nl2br(htmlspecialchars($business['deskripsi'])) ?></p>

        <!-- Fitur/tag (hanya jika ada kolom fitur di DB) -->
        <?php if (!empty($business['fitur'])): ?>
        <div class="place-features">
            <?php foreach (explode(',', $business['fitur']) as $f): ?>
                <span><?= htmlspecialchars(trim($f)) ?></span>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- ── DIVIDER ── -->
        <div class="place-divider"><span>Ulasan Pengunjung</span></div>

        <!-- Review list -->
        <?php if (mysqli_num_rows($reviewQuery) > 0): ?>
            <div class="review-list">
            <?php while ($review = mysqli_fetch_assoc($reviewQuery)):
                $rStars = (int)$review['rating'];
            ?>
                <div class="review-item-new">
                    <div class="review-item-top">
                        <div class="review-avatar">
                            <?= mb_substr($review['nama'], 0, 1) ?>
                        </div>
                        <div>
                            <strong class="review-user"><?= htmlspecialchars($review['nama']) ?></strong>
                            <div class="review-stars-inline">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <span class="<?= $i <= $rStars ? 'sh-star-on' : 'sh-star-off' ?>" style="font-size:13px">★</span>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <span class="review-date-badge">
                            <?= date('d M Y', strtotime($review['created_at'])) ?>
                        </span>
                    </div>
                    <?php if ($review['komentar']): ?>
                        <p class="review-body"><?= htmlspecialchars($review['komentar']) ?></p>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="empty-hint">Belum ada review. Jadilah yang pertama! 👇</p>
        <?php endif; ?>

        <!-- ── FORM REVIEW ── -->
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php if ($sudahReview): ?>
                <div class="already-reviewed">✅ Kamu sudah memberi review untuk tempat ini.</div>
            <?php else: ?>
                <div class="place-divider"><span>Tulis Reviewmu</span></div>

                <form action="submit_review.php" method="POST" enctype="multipart/form-data" class="review-form-new">
                    <input type="hidden" name="business_id" value="<?= $business['business_id'] ?>">

                    <!-- Star picker -->
                    <div class="star-picker-wrap">
                        <p class="star-picker-label">Rating kamu:</p>
                        <div class="star-picker-row" id="starPicker">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <button type="button" class="sp-star" data-val="<?= $i ?>"
                                        onclick="setRating(<?= $i ?>)"
                                        onmouseover="hoverRating(<?= $i ?>)"
                                        onmouseleave="resetHover()">★</button>
                            <?php endfor; ?>
                        </div>
                        <input type="hidden" name="rating" id="ratingInput" required>
                        <span class="star-picker-hint" id="starHint">Pilih bintang</span>
                    </div>

                    <textarea name="komentar" class="review-textarea"
                              placeholder="Ceritakan pengalamanmu di sini…" rows="3" required></textarea>

                    <div class="review-form-row">
                        <label class="review-file-label">
                            📷 Lampirkan foto
                            <input type="file" name="foto" accept="image/*" style="display:none"
                                   onchange="showFileName(this)">
                        </label>
                        <span class="review-file-name" id="reviewFileName">Belum ada file</span>
                        <input type="text" name="caption" class="review-caption"
                               placeholder="Caption foto (opsional)">
                    </div>

                    <button type="submit" class="btn-submit-review">Kirim Review ✉️</button>
                </form>
            <?php endif; ?>

        <?php elseif (isset($_SESSION['seller_id'])): ?>
            <p class="login-hint">Login sebagai pengguna untuk menulis review.</p>
        <?php else: ?>
            <p class="login-hint">
                <a href="login.php?message=user">Login</a> untuk menulis review.
            </p>
        <?php endif; ?>

        <!-- ── DOKUMENTASI FOTO ── -->
        <div class="place-divider"><span>Foto Pengunjung</span></div>

        <?php if (mysqli_num_rows($photoQuery) > 0): ?>
            <div class="photo-grid">
            <?php while ($foto = mysqli_fetch_assoc($photoQuery)): ?>
                <div class="user-photo">
                    <img src="uploads/documentation/<?= htmlspecialchars($foto['foto']) ?>"
                         alt="Dokumentasi"
                         onerror="this.style.display='none'">
                    <div class="photo-info">
                        <strong><?= htmlspecialchars($foto['nama']) ?></strong>
                        <?php if ($foto['caption']): ?>
                            <p><?= htmlspecialchars($foto['caption']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="empty-hint">Belum ada foto dokumentasi.</p>
        <?php endif; ?>

        <!-- Upload foto terpisah -->
        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="place-divider"><span>Upload Fotomu</span></div>
            <form action="upload_photo.php" method="POST" enctype="multipart/form-data" class="review-form-new">
                <input type="hidden" name="business_id" value="<?= $business['business_id'] ?>">
                <div class="review-form-row">
                    <label class="review-file-label">
                        📷 Pilih foto
                        <input type="file" name="foto" accept="image/*" required style="display:none"
                               onchange="showFileNameUpload(this)">
                    </label>
                    <span class="review-file-name" id="uploadFileName">Belum ada file</span>
                    <input type="text" name="caption" class="review-caption" placeholder="Caption foto…">
                </div>
                <button type="submit" class="btn-submit-review">Upload 📸</button>
            </form>
        <?php endif; ?>

    </div><!-- /place-card-body -->
</div><!-- /place-detail -->

<script>
/* ── Star picker ── */
let currentRating = 0;
const ratingLabels = ['', 'Jelek 😕', 'Kurang 😐', 'Cukup 🙂', 'Bagus 😊', 'Sempurna 🤩'];

function setRating(val) {
    currentRating = val;
    document.getElementById('ratingInput').value = val;
    document.getElementById('starHint').textContent = ratingLabels[val];
    paintStars(val, true);
}

function hoverRating(val) {
    paintStars(val, false);
    document.getElementById('starHint').textContent = ratingLabels[val];
}

function resetHover() {
    paintStars(currentRating, true);
    document.getElementById('starHint').textContent =
        currentRating ? ratingLabels[currentRating] : 'Pilih bintang';
}

function paintStars(val, committed) {
    document.querySelectorAll('.sp-star').forEach((btn, i) => {
        const on = i < val;
        btn.classList.toggle('sp-on', on);
        btn.classList.toggle('sp-hover', on && !committed);
    });
}

/* ── File name display ── */
function showFileName(input) {
    document.getElementById('reviewFileName').textContent =
        input.files[0] ? input.files[0].name : 'Belum ada file';
}
function showFileNameUpload(input) {
    document.getElementById('uploadFileName').textContent =
        input.files[0] ? input.files[0].name : 'Belum ada file';
}
</script>