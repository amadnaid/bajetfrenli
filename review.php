<?php

require 'config.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$businessQuery = mysqli_query(
    $conn,
    "SELECT business_id, nama_tempat
     FROM businesses
     ORDER BY nama_tempat ASC"
);

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Tulis Review</title>

<link rel="stylesheet" href="review.css">

</head>

<body>

<div class="review-container">

    <div class="review-header">

        <h1>Tulis Reviewmu</h1>

        <p>
            Bagikan pengalamanmu dan bantu pengguna lain menemukan tempat terbaik.
        </p>

    </div>

    <?php if(isset($_GET['success'])): ?>

        <div class="success-box">
            Review berhasil dikirim!
        </div>

    <?php endif; ?>

    <form
        action="submit_review.php"
        method="POST"
        enctype="multipart/form-data"
        class="review-form">

        <!-- TEMPAT -->

        <div class="form-group">

            <label>Pilih Tempat</label>

            <select
                name="business_id"
                required>

                <option value="">
                    -- Pilih Tempat --
                </option>

                <?php while($place = mysqli_fetch_assoc($businessQuery)): ?>

                    <option
                        value="<?= $place['business_id']; ?>">

                        <?= htmlspecialchars(
                            $place['nama_tempat']
                        ); ?>

                    </option>

                <?php endwhile; ?>

            </select>

        </div>

        <!-- RATING -->

        <div class="form-group">

            <label>Rating</label>

            <div class="star-rating">

                <input type="radio" id="star5" name="rating" value="5" required>
                <label for="star5">★</label>

                <input type="radio" id="star4" name="rating" value="4">
                <label for="star4">★</label>

                <input type="radio" id="star3" name="rating" value="3">
                <label for="star3">★</label>

                <input type="radio" id="star2" name="rating" value="2">
                <label for="star2">★</label>

                <input type="radio" id="star1" name="rating" value="1">
                <label for="star1">★</label>

            </div>

        </div>

        <!-- REVIEW -->

        <div class="form-group">

            <label>Review</label>

            <textarea
                name="komentar"
                rows="6"
                required
                placeholder="Ceritakan pengalamanmu..."></textarea>

        </div>

        <!-- FOTO -->

        <div class="form-group">

            <label>Upload Dokumentasi</label>

            <input
                type="file"
                name="foto"
                accept="image/*">

        </div>

        <!-- CAPTION -->

        <div class="form-group">

            <label>Caption Foto</label>

            <input
                type="text"
                name="caption"
                placeholder="Contoh: Nongkrong malam minggu">

        </div>

        <button
            type="submit"
            class="submit-btn">

            Kirim Review

        </button>

    </form>

</div>

</body>
</html>