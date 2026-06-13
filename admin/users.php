<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

if (isset($_GET['delete'])) {
    $user_id = $_GET['delete'];
    
    // Hapus tickets yang terhubung ke transaksi user ini
    $pdo->prepare("
        DELETE tickets FROM tickets 
        INNER JOIN transactions ON tickets.transaction_id = transactions.transaction_id 
        WHERE transactions.user_id = ?
    ")->execute([$user_id]);
    
    // Hapus transaksi user ini
    $pdo->prepare("DELETE FROM transactions WHERE user_id = ?")->execute([$user_id]);
    
    // Hapus user
    $pdo->prepare("DELETE FROM users WHERE user_id = ? AND role != 'admin'")->execute([$user_id]);
    
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