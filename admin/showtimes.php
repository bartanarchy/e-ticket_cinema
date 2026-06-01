<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM showtimes WHERE showtime_id = ?");
    $stmt->execute([$_GET['delete']]);
    header('Location: showtimes.php?success=deleted');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'add') {
    $stmt = $pdo->prepare("INSERT INTO showtimes (movie_id, date, time, hall_number, price) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$_POST['movie_id'], $_POST['date'], $_POST['time'], $_POST['hall_number'], $_POST['price']]);
    header('Location: showtimes.php?success=added');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'edit') {
    $stmt = $pdo->prepare("UPDATE showtimes SET movie_id=?, date=?, time=?, hall_number=?, price=? WHERE showtime_id=?");
    $stmt->execute([$_POST['movie_id'], $_POST['date'], $_POST['time'], $_POST['hall_number'], $_POST['price'], $_POST['showtime_id']]);
    header('Location: showtimes.php?success=updated');
    exit;
}

$showtimes = $pdo->query("
    SELECT showtimes.*, movies.title 
    FROM showtimes 
    INNER JOIN movies ON showtimes.movie_id = movies.movie_id 
    ORDER BY showtimes.date DESC, showtimes.time ASC
")->fetchAll();

$movies = $pdo->query("SELECT movie_id, title FROM movies ORDER BY title ASC")->fetchAll();

$edit_showtime = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM showtimes WHERE showtime_id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_showtime = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Showtimes - Admin CinemaTicket</title>
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
        .card-form {
            background: linear-gradient(145deg, #804A8A33, #3A035366);
            border: 1px solid rgba(245,158,81,0.2);
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 30px;
        }
        .form-label {
            color: var(--accent-light);
            font-weight: 500;
        }
        .form-control, .form-select {
            background-color: rgba(255,255,255,0.1);
            border: 1px solid rgba(248,210,153,0.3);
            color: white;
            border-radius: 8px;
        }
        .form-control::placeholder {
            color: rgba(255,255,255,0.4);
        }
        .form-control:focus, .form-select:focus {
            background-color: rgba(255,255,255,0.15);
            border-color: var(--accent);
            color: white;
            box-shadow: 0 0 0 0.2rem rgba(245,158,81,0.25);
        }
        .form-select option {
            background-color: var(--primary);
            color: white;
        }
        .btn-submit {
            background: linear-gradient(90deg, #F8D299, #F59E51);
            border: none;
            color: var(--primary);
            font-weight: 700;
            border-radius: 8px;
            padding: 10px 24px;
            transition: opacity 0.2s;
        }
        .btn-submit:hover {
            opacity: 0.85;
            color: var(--primary);
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
        .btn-edit {
            background: rgba(245,158,81,0.2);
            color: var(--accent);
            border: none;
            border-radius: 6px;
            padding: 4px 12px;
            font-size: 0.85rem;
            text-decoration: none;
        }
        .btn-edit:hover {
            background: rgba(245,158,81,0.4);
            color: var(--accent);
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
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <a href="/e-ticket_cinema/admin/index.php" class="sidebar-brand">⚙️ Admin Panel</a>
        <ul class="sidebar-menu">
            <li><a href="index.php">📊 Dashboard</a></li>
            <li><a href="movies.php">🎬 Movies</a></li>
            <li><a href="showtimes.php" class="active">🕐 Showtimes</a></li>
            <li><a href="transactions.php">💳 Transactions</a></li>
            <li><a href="users.php">👥 Users</a></li>
            <li><a href="/e-ticket_cinema/index.php">🏠 Back to Site</a></li>
            <li><a href="/e-ticket_cinema/logout.php" style="color: #ff6b7a;">🚪 Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h1 class="page-title">🕐 Showtimes</h1>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert border-0 mb-4" style="background-color: rgba(25,135,84,0.2); color: #75e0a7;" id="successAlert">
                ✅
                <?php
                    if ($_GET['success'] === 'added') echo 'Showtime berhasil ditambahkan!';
                    if ($_GET['success'] === 'updated') echo 'Showtime berhasil diupdate!';
                    if ($_GET['success'] === 'deleted') echo 'Showtime berhasil dihapus!';
                ?>
            </div>
        <?php endif; ?>

        <!-- ini form buat edit -->
        <div class="card-form">
            <h5 style="color: var(--accent-light); margin-bottom: 20px;">
                <?= $edit_showtime ? '✏️ Edit Showtime' : '➕ Add New Showtime' ?>
            </h5>
            <form method="POST">
                <input type="hidden" name="action" value="<?= $edit_showtime ? 'edit' : 'add' ?>">
                <?php if ($edit_showtime): ?>
                    <input type="hidden" name="showtime_id" value="<?= $edit_showtime['showtime_id'] ?>">
                <?php endif; ?>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Movie</label>
                        <select name="movie_id" class="form-select" required>
                            <option value="">Select movie</option>
                            <?php foreach ($movies as $movie): ?>
                            <option value="<?= $movie['movie_id'] ?>" <?= ($edit_showtime['movie_id'] ?? '') == $movie['movie_id'] ? 'selected' : '' ?>>
                                <?= $movie['title'] ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Date</label>
                        <input type="date" name="date" class="form-control" value="<?= $edit_showtime['date'] ?? '' ?>" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Time</label>
                        <input type="time" name="time" class="form-control" value="<?= $edit_showtime['time'] ?? '' ?>" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Hall Number</label>
                        <select name="hall_number" class="form-select" required>
                            <option value="">Select hall</option>
                            <option value="1" <?= ($edit_showtime['hall_number'] ?? '') == 1 ? 'selected' : '' ?>>Hall 1</option>
                            <option value="2" <?= ($edit_showtime['hall_number'] ?? '') == 2 ? 'selected' : '' ?>>Hall 2</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Price (Rp)</label>
                        <input type="number" name="price" class="form-control" placeholder="50000" value="<?= $edit_showtime['price'] ?? '' ?>" required>
                    </div>
                </div>

                <div class="mt-3 d-flex gap-2">
                    <button type="submit" class="btn-submit">
                        <?= $edit_showtime ? '💾 Update Showtime' : '➕ Add Showtime' ?>
                    </button>
                    <?php if ($edit_showtime): ?>
                        <a href="showtimes.php" class="btn-edit" style="padding: 10px 24px;">✖ Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- ini Tabel Showtimes -->
        <div style="background: linear-gradient(145deg, #804A8A33, #3A035333); border-radius: 12px; overflow: hidden;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Movie</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Hall</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($showtimes as $show): ?>
                    <tr>
                        <td><?= $show['title'] ?></td>
                        <td><?= date('d M Y', strtotime($show['date'])) ?></td>
                        <td><?= date('H:i', strtotime($show['time'])) ?></td>
                        <td>Hall <?= $show['hall_number'] ?></td>
                        <td>Rp <?= number_format($show['price'], 0, ',', '.') ?></td>
                        <td class="d-flex gap-2">
                            <a href="showtimes.php?edit=<?= $show['showtime_id'] ?>" class="btn-edit">✏️ Edit</a>
                            <a href="showtimes.php?delete=<?= $show['showtime_id'] ?>" class="btn-delete" onclick="return confirm('Hapus showtime ini?')">🗑️ Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($showtimes)): ?>
                    <tr>
                        <td colspan="6" class="text-center" style="color: rgba(255,255,255,0.4); padding: 30px;">No showtimes yet</td>
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