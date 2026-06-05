    <?php
    require 'config.php';

    if(isset($_SESSION['user_id']) || isset($_SESSION['seller_id'])){
        header("Location: index.php");
        exit();
    }
    ?>

    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login - Surabaya Life On a Budget</title>
        <link rel="stylesheet" href="login.css">
    </head>
    <body class="login-page">

    <div class="login-container">
    <?php
        $warning = '';
        if(isset($_GET['message'])){
            if($_GET['message'] == 'seller'){
                $warning =
                'Untuk mendaftarkan usaha, silakan login sebagai penjual terlebih dahulu.';
            }

            if($_GET['message'] == 'user'){
                $warning =
                'Untuk menulis review, silakan login sebagai pengguna terlebih dahulu.';
            }
        }
        ?>
        <?php if(!empty($warning)): ?>
        <div class="warning-box">
            <?= htmlspecialchars($warning); ?>
        </div>
    <?php endif; ?>

        <h1>Surabaya Life On a Budget</h1>
        <p>Pilih jenis akun yang ingin digunakan</p>

        <div class="role-selection">
            <button onclick="showLogin('user')">
                Login sebagai Pengguna
            </button>

            <button onclick="showLogin('seller')">
                Login sebagai Penjual
            </button>
        </div>

        <!-- Form Pengguna -->
        <div id="userLogin" class="login-form hidden">
            <h2>Login Pengguna</h2>

            <form action="login_user.php" method="POST">
                <input type="email"
                    name="email"
                    placeholder="Email"
                    required>

                <input type="password"
                    name="password"
                    placeholder="Password"
                    required>

                <button type="submit">
                    Masuk
                </button>
            </form>

            <p>
                Belum punya akun?
                <a href="register_user.php">Daftar Pengguna</a>
            </p>
        </div>

        <!-- Form Penjual -->
        <div id="sellerLogin" class="login-form hidden">
            <h2>Login Penjual</h2>

            <form action="login_seller.php" method="POST">
                <input type="email"
                    name="email"
                    placeholder="Email Usaha"
                    required>

                <input type="password"
                    name="password"
                    placeholder="Password"
                    required>

                <button type="submit">
                    Masuk
                </button>
            </form>

            <p>
                Belum terdaftar?
                <a href="register_seller.php">
                    Daftarkan Usaha
                </a>
            </p>
        </div>

    </div>

    <script>
    function showLogin(role) {

        document.getElementById('userLogin')
                .classList.add('hidden');

        document.getElementById('sellerLogin')
                .classList.add('hidden');

        if(role === 'user'){
            document.getElementById('userLogin')
                    .classList.remove('hidden');
        }

        if(role === 'seller'){
            document.getElementById('sellerLogin')
                    .classList.remove('hidden');
        }
    }

    window.onload = function(){

    <?php if(isset($_GET['message']) && $_GET['message'] == 'user'): ?>

        showLogin('user');

    <?php endif; ?>

    <?php if(isset($_GET['message']) && $_GET['message'] == 'seller'): ?>

        showLogin('seller');

    <?php endif; ?>

    }
    </script>

    </body>
    </html>