<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$transactions = $pdo->query("
    SELECT transactions.*, users.name as user_name,
    COUNT(tickets.ticket_id) as total_tickets
    FROM transactions
    INNER JOIN users ON transactions.user_id = users.user_id
    LEFT JOIN tickets ON transactions.transaction_id = tickets.transaction_id
    GROUP BY transactions.transaction_id, transactions.user_id, transactions.transaction_date,
    transactions.total_amount, transactions.payment_method, users.name
    ORDER BY transactions.transaction_date DESC
")->fetchAll();

$total_revenue = $pdo->query("SELECT SUM(total_amount) FROM transactions")->fetchColumn();
$total_transactions = $pdo->query("SELECT COUNT(*) FROM transactions")->fetchColumn();
$total_tickets_sold = $pdo->query("SELECT COUNT(*) FROM tickets WHERE status = 'booked'")->fetchColumn();
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
        <h1 class="page-title">💳 Transactions</h1>

        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon">💳</div>
                    <div class="stat-number"><?= $total_transactions ?></div>
                    <div class="stat-label">Total Transactions</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon">🎟️</div>
                    <div class="stat-number"><?= $total_tickets_sold ?></div>
                    <div class="stat-label">Tickets Sold</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon">💰</div>
                    <div class="stat-number">Rp <?= number_format($total_revenue ?? 0, 0, ',', '.') ?></div>
                    <div class="stat-label">Total Revenue</div>
                </div>
            </div>
        </div>

        <div style="background: linear-gradient(145deg, #804A8A33, #3A035333); border-radius: 12px; overflow: hidden;">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Date</th>
                        <th>Tickets</th>
                        <th>Payment Method</th>
                        <th>Total Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $trx): ?>
                        <tr>
                            <td style="color: rgba(255,255,255,0.4);">#<?= $trx['transaction_id'] ?></td>
                            <td>👤 <?= $trx['user_name'] ?></td>
                            <td><?= date('d M Y, H:i', strtotime($trx['transaction_date'])) ?></td>
                            <td>🎟️ <?= $trx['total_tickets'] ?> ticket(s)</td>
                            <td>
                                <span class="badge-method <?= $trx['payment_method'] === 'transfer' ? 'badge-transfer' : ($trx['payment_method'] === 'cash' ? 'badge-cash' : 'badge-ewallet') ?>">
                                    <?= ucfirst($trx['payment_method']) ?>
                                </span>
                            </td>
                            <td style="color: var(--accent); font-weight: 700;">
                                Rp <?= number_format($trx['total_amount'], 0, ',', '.') ?>
                            </td>
                            <td>
                                <a href="transaction_detail.php?id=<?= $trx['transaction_id'] ?>" class="btn-detail">🔍 Detail</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($transactions)): ?>
                        <tr>
                            <td colspan="7" class="text-center" style="color: rgba(255,255,255,0.4); padding: 30px;">No transactions yet</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="/e-ticket_cinema/assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>