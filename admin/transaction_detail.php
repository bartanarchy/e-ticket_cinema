<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: transactions.php');
    exit;
}

$transaction_id = $_GET['id'];

// GET detail transaksi
$stmt = $pdo->prepare("
    SELECT transactions.*, users.name as user_name, users.email
    FROM transactions
    INNER JOIN users ON transactions.user_id = users.user_id
    WHERE transactions.transaction_id = ?
");
$stmt->execute([$transaction_id]);
$transaction = $stmt->fetch();

if (!$transaction) {
    header('Location: transactions.php');
    exit;
}

// GET tiket dalam transaksi ini
$stmt = $pdo->prepare("
    SELECT tickets.*, seats.seat_code, movies.title as movie_title,
    showtimes.date as show_date, showtimes.time as show_time,
    showtimes.hall_number, showtimes.price
    FROM tickets
    INNER JOIN seats ON tickets.seat_id = seats.seat_id
    INNER JOIN showtimes ON tickets.showtime_id = showtimes.showtime_id
    INNER JOIN movies ON showtimes.movie_id = movies.movie_id
    WHERE tickets.transaction_id = ?
");
$stmt->execute([$transaction_id]);
$tickets = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Detail - Admin CinemaTicket</title>
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
            border-right: 1px solid rgba(245,158,81,0.2);
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
            border-bottom: 1px solid rgba(245,158,81,0.2);
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
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: all 0.2s;
            font-weight: 500;
        }
        .sidebar-menu li a:hover,
        .sidebar-menu li a.active {
            background: rgba(245,158,81,0.1);
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
        .detail-card {
            background: linear-gradient(145deg, #804A8A33, #3A035366);
            border: 1px solid rgba(245,158,81,0.2);
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
        }
        .detail-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            color: rgba(255,255,255,0.7);
        }
        .detail-item:last-child {
            border-bottom: none;
        }
        .detail-item span:last-child {
            color: white;
            font-weight: 500;
        }
        .table {
            color: white;
        }
        .table thead th {
            background: rgba(245,158,81,0.1);
            border-color: rgba(245,158,81,0.2);
            color: var(--accent);
        }
        .table td {
            border-color: rgba(255,255,255,0.05);
            vertical-align: middle;
        }
        .badge-booked {
            background: rgba(25,135,84,0.2);
            color: #75e0a7;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
        }
        .badge-canceled {
            background: rgba(220,53,69,0.2);
            color: #ff6b7a;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
        }
        .btn-back {
            background: rgba(245,158,81,0.2);
            color: var(--accent);
            border: 1px solid rgba(245,158,81,0.3);
            border-radius: 8px;
            padding: 8px 20px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
        }
        .btn-back:hover {
            background: rgba(245,158,81,0.3);
            color: var(--accent);
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <a href="/e-ticket_cinema/admin/index.php" class="sidebar-brand">⚙️ Admin Panel</a>
        <ul class="sidebar-menu">
            <li><a href="index.php">📊 Dashboard</a></li>
            <li><a href="movies.php">🎬 Movies</a></li>
            <li><a href="showtimes.php">🕐 Showtimes</a></li>
            <li><a href="transactions.php" class="active">💳 Transactions</a></li>
            <li><a href="users.php">👥 Users</a></li>
            <li><a href="/e-ticket_cinema/index.php">🏠 Back to Site</a></li>
            <li><a href="/e-ticket_cinema/logout.php" style="color: #ff6b7a;">🚪 Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="transactions.php" class="btn-back">← Back</a>
            <h1 class="page-title mb-0">💳 Transaction #<?= $transaction_id ?></h1>
        </div>

        <div class="row g-4">
            <!-- Info Transaksi -->
            <div class="col-md-6">
                <div class="detail-card">
                    <h5 style="color: var(--accent-light); margin-bottom: 15px;">📋 Transaction Info</h5>
                    <div class="detail-item">
                        <span>Transaction ID</span>
                        <span>#<?= $transaction['transaction_id'] ?></span>
                    </div>
                    <div class="detail-item">
                        <span>Date</span>
                        <span><?= date('d M Y, H:i', strtotime($transaction['transaction_date'])) ?></span>
                    </div>
                    <div class="detail-item">
                        <span>Payment Method</span>
                        <span>
                            <?php
                            if ($transaction['payment_method'] === 'transfer') echo '🏦 Transfer';
                            elseif ($transaction['payment_method'] === 'cash') echo '💵 Cash';
                            else echo '📱 E-Wallet';
                            ?>
                        </span>
                    </div>
                    <div class="detail-item">
                        <span>Total Amount</span>
                        <span style="color: var(--accent); font-weight: 700;">
                            Rp <?= number_format($transaction['total_amount'], 0, ',', '.') ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Info User -->
            <div class="col-md-6">
                <div class="detail-card">
                    <h5 style="color: var(--accent-light); margin-bottom: 15px;">👤 Customer Info</h5>
                    <div class="detail-item">
                        <span>Name</span>
                        <span><?= $transaction['user_name'] ?></span>
                    </div>
                    <div class="detail-item">
                        <span>Email</span>
                        <span><?= $transaction['email'] ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tiket -->
        <h5 style="color: var(--accent-light); margin-bottom: 15px;">🎟️ Tickets</h5>
        <div style="background: linear-gradient(145deg, #804A8A33, #3A035333); border-radius: 12px; overflow: hidden;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Movie</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Hall</th>
                        <th>Seat</th>
                        <th>Price</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tickets as $ticket): ?>
                    <tr>
                        <td><?= $ticket['movie_title'] ?></td>
                        <td><?= date('d M Y', strtotime($ticket['show_date'])) ?></td>
                        <td><?= date('H:i', strtotime($ticket['show_time'])) ?></td>
                        <td>Hall <?= $ticket['hall_number'] ?></td>
                        <td><span style="background: rgba(245,158,81,0.2); color: var(--accent); padding: 3px 8px; border-radius: 6px; font-weight: 600;"><?= $ticket['seat_code'] ?></span></td>
                        <td>Rp <?= number_format($ticket['price'], 0, ',', '.') ?></td>
                        <td>
                            <span class="<?= $ticket['status'] === 'booked' ? 'badge-booked' : 'badge-canceled' ?>">
                                <?= ucfirst($ticket['status']) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="/e-ticket_cinema/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>