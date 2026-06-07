<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ? AND role != 'admin'");
    $stmt->execute([$_GET['delete']]);
    header('Location: users.php?success=deleted');
    exit;
}

$users = $pdo->query("
    SELECT users.*, COUNT(transactions.transaction_id) as total_transactions
    FROM users
    LEFT JOIN transactions ON users.user_id = transactions.user_id
    GROUP BY users.user_id, users.name, users.email, users.role, users.created_at
    ORDER BY users.created_at DESC
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users - Admin CinemaTicket</title>
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
        .table {
            color: white;
            --bs-table-bg: transparent;
            --bs-table-striped-bg: transparent;
            --bs-table-hover-bg: rgba(255,255,255,0.05);
            --bs-table-color: white;
            --bs-table-border-color: rgba(255,255,255,0.05);
        }
        .table > :not(caption) > * > * {
            background-color: transparent;
            color: white;
        }
        .table thead th {
            background: rgba(245,158,81,0.1) !important;
            border-color: rgba(245,158,81,0.2);
            color: var(--accent);
        }
        .table td {
            border-color: rgba(255,255,255,0.05);
            vertical-align: middle;
        }
        .badge-role {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-admin {
            background: rgba(245,158,81,0.2);
            color: var(--accent);
        }
        .badge-user {
            background: rgba(255,255,255,0.1);
            color: rgba(255,255,255,0.7);
        }
        .btn-delete {
            background: rgba(220,53,69,0.2);
            color: #ff6b7a;
            border: none;
            border-radius: 6px;
            padding: 4px 12px;
            font-size: 0.85rem;
            text-decoration: none;
        }
        .btn-delete:hover {
            background: rgba(220,53,69,0.4);
            color: #ff6b7a;
        }
        .avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #804A8A, #3A0353);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: var(--accent);
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <a href="/e-ticket_cinema/admin/index.php" class="sidebar-brand">⚙️ Admin Panel</a>
        <ul class="sidebar-menu">
            <li><a href="index.php">📊 Dashboard</a></li>
            <li><a href="movies.php">🎬 Movies</a></li>
            <li><a href="showtimes.php">🕐 Showtimes</a></li>
            <li><a href="transactions.php">💳 Transactions</a></li>
            <li><a href="users.php" class="active">👥 Users</a></li>
            <li><a href="/e-ticket_cinema/index.php">🏠 Back to Site</a></li>
            <li><a href="/e-ticket_cinema/logout.php" style="color: #ff6b7a;">🚪 Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <h1 class="page-title">👥 Users</h1>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert border-0 mb-4" style="background-color: rgba(25,135,84,0.2); color: #75e0a7;" id="successAlert">
                ✅ User berhasil dihapus!
            </div>
        <?php endif; ?>

        <div style="background: linear-gradient(145deg, #804A8A33, #3A035333); border-radius: 12px; overflow: hidden;">
            <table class="table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Transactions</th>
                        <th>Joined</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar"><?= strtoupper(substr($user['name'], 0, 1)) ?></div>
                                <?= $user['name'] ?>
                            </div>
                        </td>
                        <td style="color: rgba(255,255,255,0.6);"><?= $user['email'] ?></td>
                        <td>
                            <span class="badge-role <?= $user['role'] === 'admin' ? 'badge-admin' : 'badge-user' ?>">
                                <?= ucfirst($user['role']) ?>
                            </span>
                        </td>
                        <td>💳 <?= $user['total_transactions'] ?></td>
                        <td style="color: rgba(255,255,255,0.6);"><?= date('d M Y', strtotime($user['created_at'])) ?></td>
                        <td>
                            <?php if ($user['role'] !== 'admin'): ?>
                                <a href="users.php?delete=<?= $user['user_id'] ?>" class="btn-delete" onclick="return confirm('Hapus user ini?')">🗑️ Delete</a>
                            <?php else: ?>
                                <span style="color: rgba(255,255,255,0.3); font-size: 0.85rem;">Protected</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="6" class="text-center" style="color: rgba(255,255,255,0.4); padding: 30px;">No users yet</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="/e-ticket_cinema/assets/js/bootstrap.bundle.min.js"></script>
    <script>
        setTimeout(() => {
            const alert = document.querySelector('#successAlert');
            if (alert) {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }
        }, 3000);
    </script>
</body>
</html>