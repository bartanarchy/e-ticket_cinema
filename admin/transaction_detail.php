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
    <title>... - Admin CinemaTicket</title>
    <link href="/e-ticket_cinema/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/e-ticket_cinema/assets/css/style.css" rel="stylesheet">
</head>

<body>
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

    <div class="main-content">
        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="transactions.php" class="btn-back">← Back</a>
            <h1 class="page-title mb-0">💳 Transaction #<?= $transaction_id ?></h1>
        </div>

        <div class="row g-4">
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