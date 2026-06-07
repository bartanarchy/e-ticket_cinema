<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// DELETE
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM showtimes WHERE showtime_id = ?");
    $stmt->execute([$_GET['delete']]);
    header('Location: showtimes.php?success=deleted');
    exit;
}

// INSERT
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'add') {
    $stmt = $pdo->prepare("INSERT INTO showtimes (movie_id, date, time, hall_number, price) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$_POST['movie_id'], $_POST['date'], $_POST['time'], $_POST['hall_number'], $_POST['price']]);
    header('Location: showtimes.php?success=added');
    exit;
}

// UPDATE
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
            <li><a href="showtimes.php" class="active">🕐 Showtimes</a></li>
            <li><a href="transactions.php">💳 Transactions</a></li>
            <li><a href="users.php">👥 Users</a></li>
            <li><a href="/e-ticket_cinema/index.php">🏠 Back to Site</a></li>
            <li><a href="/e-ticket_cinema/logout.php" style="color: #ff6b7a;">🚪 Logout</a></li>
        </ul>
    </div>

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