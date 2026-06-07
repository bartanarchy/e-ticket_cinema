<?php
session_start();
require_once '../config/db.php';

// ini buat ngecek apakah user udah login dan role-nya admin, kalo gak ya balik ke halaman login lagi gi
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// ini buat liat statistik dashboard
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
    <style>
        :root {
            --primary: #3A0353;
            --secondary: #804A8A;
            --accent: #F59E51;
            --accent-light: #F8D299;
            --dark: #1a0230;
        }

        body {
            background-color: var(--dark);
            color: white;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            min-height: 100vh;
            background: linear-gradient(180deg, #3A0353, #1a0230);
            border-right: 1px solid rgba(245, 158, 81, 0.2);
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 20px;
        }

        .sidebar-brand {
            background: linear-gradient(90deg, #F8D299, #F59E51);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 700;
            font-size: 1.3rem;
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(245, 158, 81, 0.2);
            display: block;
            text-decoration: none;
        }

        .sidebar-menu {
            list-style: none;
            padding: 20px 0;
            margin: 0;
        }

        .sidebar-menu li a {
            display: block;
            padding: 12px 20px;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: all 0.2s;
            font-weight: 500;
        }

        .sidebar-menu li a:hover,
        .sidebar-menu li a.active {
            background: rgba(245, 158, 81, 0.1);
            color: var(--accent);
            border-left: 3px solid var(--accent);
        }

        .main-content {
            margin-left: 250px;
            padding: 30px;
        }

        .page-title {
            background: linear-gradient(90deg, #F8D299, #F59E51);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 700;
            font-size: 1.8rem;
            margin-bottom: 30px;
        }

        .stat-card {
            background: linear-gradient(145deg, #804A8A, #3A0353);
            border: none;
            border-radius: 12px;
            padding: 24px;
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-4px);
        }

        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 12px;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 800;
            color: var(--accent);
        }

        .stat-label {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.9rem;
        }

        .recent-table {
            background: linear-gradient(145deg, #804A8A33, #3A035333);
            border-radius: 12px;
            overflow: hidden;
        }

        .table {
            color: white;
            --bs-table-bg: transparent;
            --bs-table-striped-bg: transparent;
            --bs-table-hover-bg: rgba(255, 255, 255, 0.05);
            --bs-table-color: white;
            --bs-table-border-color: rgba(255, 255, 255, 0.05);
        }

        .table> :not(caption)>*>* {
            background-color: transparent;
            color: white;
        }

        .table thead th {
            background: rgba(245, 158, 81, 0.1) !important;
            border-color: rgba(245, 158, 81, 0.2);
            color: var(--accent);
            font-weight: 600;
        }

        .table td {
            border-color: rgba(255, 255, 255, 0.05);
            vertical-align: middle;
        }

        .badge-genre {
            background: rgba(245, 158, 81, 0.2);
            color: var(--accent);
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
        }
    </style>
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
            <li style="margin-top: auto; padding-top: 20px;">
                <a href="/e-ticket_cinema/index.php">🏠 Back to Site</a>
            </li>
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
        <div class="recent-table">
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
                                <a href="movies.php" class="btn btn-sm" style="background: rgba(245,158,81,0.2); color: var(--accent); border: none;">Manage</a>
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