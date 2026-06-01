<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// mengambil semua transaksi dengan JOIN ke users dan hitung total tiket per transaksi
$transactions = $pdo->query("
    SELECT transactions.*, users.name as user_name,
    COUNT(tickets.ticket_id) as total_tickets
    FROM transactions
    INNER JOIN users ON transactions.user_id = users.user_id
    LEFT JOIN tickets ON transactions.transaction_id = tickets.transaction_id
    GROUP BY transactions.transaction_id
    ORDER BY transactions.transaction_date DESC
")->fetchAll();

// Statistik
$total_revenue = $pdo->query("SELECT SUM(total_amount) FROM transactions")->fetchColumn();
$total_transactions = $pdo->query("SELECT COUNT(*) FROM transactions")->fetchColumn();
$total_tickets_sold = $pdo->query("SELECT COUNT(*) FROM tickets WHERE status = 'booked'")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions - Admin CinemaTicket</title>
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
            font-size: 2rem;
            margin-bottom: 8px;
        }
        .stat-number {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--accent);
        }
        .stat-label {
            color: rgba(255,255,255,0.6);
            font-size: 0.9rem;
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
        .badge-method {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-transfer {
            background: rgba(13,110,253,0.2);
            color: #6ea8fe;
        }
        .badge-cash {
            background: rgba(25,135,84,0.2);
            color: #75e0a7;
        }
        .badge-ewallet {
            background: rgba(245,158,81,0.2);
            color: var(--accent);
        }
        .btn-detail {
            background: rgba(245,158,81,0.2);
            color: var(--accent);
            border: none;
            border-radius: 6px;
            padding: 4px 12px;
            font-size: 0.85rem;
            text-decoration: none;
        }
        .btn-detail:hover {
            background: rgba(245,158,81,0.4);
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
        <h1 class="page-title">💳 Transactions</h1>

        <!-- Statistik -->
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

        <!-- Tabel Transaksi -->
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