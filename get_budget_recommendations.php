<?php
require 'config.php';

$budget = (int)$_GET['budget'];

if ($budget <= 0) {
    echo '<p class="budget-hint">Budget tidak valid.</p>';
    exit;
}

// Ambil semua usaha yang harga_min-nya <= budget user, dikelompokkan per kategori
$query = mysqli_query($conn,
    "SELECT businesses.*, categories.nama_kategori
     FROM businesses
     JOIN categories ON categories.category_id = businesses.category_id
     WHERE businesses.harga_min <= $budget
     ORDER BY categories.category_id ASC, businesses.harga_min ASC"
);

$count = mysqli_num_rows($query);

if ($count === 0): ?>
    <div class="budget-empty">
        <span>😢</span>
        <p>Belum ada tempat yang cocok dengan budget <strong>Rp<?= number_format($budget, 0, ',', '.') ?></strong>.</p>
        <p>Coba tambah sedikit budgetmu!</p>
    </div>
<?php else:
    // Kelompokkan per kategori
    $grouped = [];
    while ($row = mysqli_fetch_assoc($query)) {
        $grouped[$row['nama_kategori']][] = $row;
    }

    // Icon per kategori
    $icons = [
        'Warung on a Budget'      => '🍜',
        'Nongki on a Budget'      => '☕',
        'Nongki Non-stop'         => '🍽️',
        'Healing Murmer'          => '🌴',
        'Fashion Cewek on Budget' => '👗',
        'Fashion Cowok on Budget' => '👕',
    ];
?>
    <p class="budget-result-title">
        Dengan <strong>Rp<?= number_format($budget, 0, ',', '.') ?></strong>
        kamu bisa mengunjungi <strong><?= $count ?> tempat</strong> — klik kartu untuk lihat detail!
    </p>

    <?php foreach ($grouped as $kategori => $items): ?>
    <div class="budget-result-section">
        <div class="budget-result-section-title">
            <?= $icons[$kategori] ?? '📌' ?> <?= htmlspecialchars($kategori) ?>
        </div>
        <div class="budget-result-grid">
        <?php foreach ($items as $row): ?>
            <div class="budget-result-card" onclick="openPlace(<?= $row['business_id'] ?>)">
                <?php if ($row['foto_utama']): ?>
                    <img src="uploads/businesses/<?= htmlspecialchars($row['foto_utama']) ?>"
                         alt="<?= htmlspecialchars($row['nama_tempat']) ?>"
                         onerror="this.src='https://placehold.co/300x150/e8f5e9/2d6a4f?text=No+Image'">
                <?php else: ?>
                    <img src="https://placehold.co/300x150/e8f5e9/2d6a4f?text=No+Image" alt="">
                <?php endif; ?>
                <div class="budget-result-body">
                    <span class="budget-tag"><?= htmlspecialchars($kategori) ?></span>
                    <h5><?= htmlspecialchars($row['nama_tempat']) ?></h5>
                    <p class="price-range">
                        <?php if ($row['harga_min'] == 0): ?>
                            GRATIS
                        <?php else: ?>
                            Rp<?= number_format($row['harga_min'], 0, ',', '.') ?>
                            –
                            Rp<?= number_format($row['harga_max'], 0, ',', '.') ?>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>

<?php endif; ?>