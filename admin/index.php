<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$total_movies = $pdo->query("SELECT COUNT(*) FROM movies")->fetchColumn();
$total_users = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
$total_transactions = $pdo->query("SELECT COUNT(*) FROM transactions")->fetchColumn();
$total_revenue = $pdo->query("SELECT SUM(total_amount) FROM transactions")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - CinemaTicket</title>
    <link href="/e-ticket_cinema/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/e-ticket_cinema/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="sidebar">
        <a href="/e-ticket_cinema/admin/index.php" class="sidebar-brand">⚙️ Admin Panel</a>
        <ul class="sidebar-menu">
            <li><a href="index.php" class="active">📊 Dashboard</a></li>
            <li><a href="movies.php">🎬 Movies</a></li>
            <li><a href="showtimes.php">🕐 Showtimes</a></li>
            <li><a href="transactions.php">💳 Transactions</a></li>
            <li><a href="users.php">👥 Users</a></li>
            <li><a href="/e-ticket_cinema/index.php">🏠 Back to Site</a></li>
            <li><a href="/e-ticket_cinema/logout.php" style="color: #ff6b7a;">🚪 Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <h1 class="page-title">📊 Dashboard</h1>

        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon">🎬</div>
                    <div class="stat-number"><?= $total_movies ?></div>
                    <div class="stat-label">Total Movies</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon">👥</div>
                    <div class="stat-number"><?= $total_users ?></div>
                    <div class="stat-label">Total Users</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon">💳</div>
                    <div class="stat-number"><?= $total_transactions ?></div>
                    <div class="stat-label">Total Transactions</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon">💰</div>
                    <div class="stat-number">Rp <?= number_format($total_revenue ?? 0, 0, ',', '.') ?></div>
                    <div class="stat-label">Total Revenue</div>
                </div>
            </div>
        </div>

        <h5 style="color: var(--accent-light); margin-bottom: 15px;">🎬 Recent Movies</h5>
        <div style="background: linear-gradient(145deg, #804A8A33, #3A035333); border-radius: 12px; overflow: hidden;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Genre</th>
                        <th>Duration</th>
                        <th>Release Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $movies = $pdo->query("SELECT * FROM movies ORDER BY release_date DESC LIMIT 5")->fetchAll();
                    foreach ($movies as $movie):
                    ?>
                    <tr>
                        <td><?= $movie['title'] ?></td>
                        <td><span class="badge-genre"><?= $movie['genre'] ?></span></td>
                        <td><?= $movie['duration'] ?> min</td>
                        <td><?= $movie['release_date'] ?></td>
                        <td>
                            <a href="movies.php" class="btn-edit">Manage</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($movies)): ?>
                    <tr>
                        <td colspan="5" class="text-center" style="color: rgba(255,255,255,0.4); padding: 30px;">No movies yet</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="/e-ticket_cinema/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>