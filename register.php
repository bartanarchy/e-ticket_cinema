<?php
session_start();
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = MD5($_POST['password']);
    $confirm_password = MD5($_POST['confirm_password']);

    if ($password !== $confirm_password) {
        $error = "Password tidak cocok!";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $existing = $stmt->fetch();

        if ($existing) {
            $error = "Email sudah terdaftar!";
        } else {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
            $stmt->execute([$name, $email, $password]);
            header('Location: login.php?registered=1');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Cinema Ticket</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #2E0854;
            background-image: radial-gradient(ellipse at top, #3A0353, #1a0230);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .card {
            background: linear-gradient(145deg, #804A8A, #3A0353);
            border: none;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
        }

        .card-title {
            background: linear-gradient(90deg, #F8D299, #F59E51);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 700;
            font-size: 1.8rem;
        }

        .form-control {
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(248, 210, 153, 0.3);
            color: white;
            border-radius: 8px;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        .form-control:focus {
            background-color: rgba(255, 255, 255, 0.15);
            border-color: #F59E51;
            color: white;
            box-shadow: 0 0 0 0.2rem rgba(245, 158, 81, 0.25);
        }

        .form-label {
            color: #F8D299;
            font-weight: 500;
        }

        .btn-register {
            background: linear-gradient(90deg, #F8D299, #F59E51);
            border: none;
            color: #3A0353;
            font-weight: 700;
            border-radius: 8px;
            padding: 10px;
            transition: opacity 0.2s;
        }

        .btn-register:hover {
            opacity: 0.85;
            color: #3A0353;
        }

        .login-link {
            color: #F59E51;
            text-decoration: none;
            font-weight: 500;
        }

        .login-link:hover {
            color: #F8D299;
        }

        .text-muted-light {
            color: rgba(255, 255, 255, 0.5);
        }
    </style>
</head>

<body>
    <div class="card p-4" style="width: 420px;">
        <h3 class="card-title text-center mb-1">🎬 CinemaTicket</h3>
        <p class="text-center text-muted-light mb-4" style="font-size: 0.85rem;">Buat akun baru</p>

        <?php if (isset($error)): ?>
            <div class="alert border-0" style="background-color: rgba(220,53,69,0.2); color: #ff6b7a;">
                ❌ <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="name" class="form-control" placeholder="Masukkan nama lengkap" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" placeholder="Masukkan email" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
            </div>
            <div class="mb-4">
                <label class="form-label">Konfirmasi Password</label>
                <input type="password" name="confirm_password" class="form-control" placeholder="Ulangi password" required>
            </div>
            <button type="submit" class="btn btn-register w-100">Daftar</button>
        </form>

        <p class="text-center mt-3 text-muted-light">Sudah punya akun?
            <a href="login.php" class="login-link">Login di sini</a>
        </p>
    </div>
    <p class="text-center mt-3" style="color: rgba(255,255,255,0.3); font-size: 0.75rem;">
        © 2026 CinemaTicket. Sebian & Irgi. All rights reserved.
    </p>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script>
        setTimeout(() => {
            const alert = document.querySelector('.alert');
            if (alert) {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }
        }, 3000);
    </script>
</body>

</html>