<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// ini buat DELETE
// DELETE
if (isset($_GET['delete'])) {
    $movie_id = $_GET['delete'];

    // Hapus tickets yang terhubung ke showtimes film ini
    $pdo->prepare("
        DELETE tickets FROM tickets 
        INNER JOIN showtimes ON tickets.showtime_id = showtimes.showtime_id 
        WHERE showtimes.movie_id = ?
    ")->execute([$movie_id]);

    // Hapus showtimes film ini
    $pdo->prepare("DELETE FROM showtimes WHERE movie_id = ?")->execute([$movie_id]);

    // Hapus film
    $pdo->prepare("DELETE FROM movies WHERE movie_id = ?")->execute([$movie_id]);

    header('Location: movies.php?success=deleted');
    exit;
}

// ini buat INSERT
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'add') {
    $title = $_POST['title'];
    $genre = $_POST['genre'];
    $duration = $_POST['duration'];
    $synopsis = $_POST['synopsis'];
    $release_date = $_POST['release_date'];
    $poster = '';

    if ($_FILES['poster']['name']) {
        $ext = pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION);
        $poster = uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['poster']['tmp_name'], '../assets/img/' . $poster);
    }

    $stmt = $pdo->prepare("INSERT INTO movies (title, genre, duration, synopsis, poster, release_date) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$title, $genre, $duration, $synopsis, $poster, $release_date]);
    header('Location: movies.php?success=added');
    exit;
}

// ini buat UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'edit') {
    $movie_id = $_POST['movie_id'];
    $title = $_POST['title'];
    $genre = $_POST['genre'];
    $duration = $_POST['duration'];
    $synopsis = $_POST['synopsis'];
    $release_date = $_POST['release_date'];

    $stmt = $pdo->prepare("SELECT poster FROM movies WHERE movie_id = ?");
    $stmt->execute([$movie_id]);
    $old = $stmt->fetch();
    $poster = $old['poster'];

    if ($_FILES['poster']['name']) {
        $ext = pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION);
        $poster = uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['poster']['tmp_name'], '../assets/img/' . $poster);
    }

    $stmt = $pdo->prepare("UPDATE movies SET title=?, genre=?, duration=?, synopsis=?, poster=?, release_date=? WHERE movie_id=?");
    $stmt->execute([$title, $genre, $duration, $synopsis, $poster, $release_date, $movie_id]);
    header('Location: movies.php?success=updated');
    exit;
}

// ini buat ngambil semua data film dari database buat ditampilin di tabel gi
$movies = $pdo->query("SELECT * FROM movies ORDER BY release_date DESC")->fetchAll();

// GET film buat edit
$edit_movie = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM movies WHERE movie_id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_movie = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movies - Admin CinemaTicket</title>
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

        .card-form {
            background: linear-gradient(145deg, #804A8A33, #3A035366);
            border: 1px solid rgba(245, 158, 81, 0.2);
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 30px;
        }

        .form-label {
            color: var(--accent-light);
            font-weight: 500;
        }

        .form-control,
        .form-select {
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(248, 210, 153, 0.3);
            color: white;
            border-radius: 8px;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        .form-control:focus,
        .form-select:focus {
            background-color: rgba(255, 255, 255, 0.15);
            border-color: var(--accent);
            color: white;
            box-shadow: 0 0 0 0.2rem rgba(245, 158, 81, 0.25);
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
            background: rgba(245, 158, 81, 0.1);
            border-color: rgba(245, 158, 81, 0.2);
            color: var(--accent);
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

        .btn-edit {
            background: rgba(245, 158, 81, 0.2);
            color: var(--accent);
            border: none;
            border-radius: 6px;
            padding: 4px 12px;
            font-size: 0.85rem;
            text-decoration: none;
        }

        .btn-edit:hover {
            background: rgba(245, 158, 81, 0.4);
            color: var(--accent);
        }

        .btn-delete {
            background: rgba(220, 53, 69, 0.2);
            color: #ff6b7a;
            border: none;
            border-radius: 6px;
            padding: 4px 12px;
            font-size: 0.85rem;
            text-decoration: none;
        }

        .btn-delete:hover {
            background: rgba(220, 53, 69, 0.4);
            color: #ff6b7a;
        }

        .movie-poster-thumb {
            width: 45px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }

        .no-poster-thumb {
            width: 45px;
            height: 60px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <a href="/e-ticket_cinema/admin/index.php" class="sidebar-brand">⚙️ Admin Panel</a>
        <ul class="sidebar-menu">
            <li><a href="index.php">📊 Dashboard</a></li>
            <li><a href="movies.php" class="active">🎬 Movies</a></li>
            <li><a href="showtimes.php">🕐 Showtimes</a></li>
            <li><a href="transactions.php">💳 Transactions</a></li>
            <li><a href="users.php">👥 Users</a></li>
            <li><a href="/e-ticket_cinema/index.php">🏠 Back to Site</a></li>
            <li><a href="/e-ticket_cinema/logout.php" style="color: #ff6b7a;">🚪 Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h1 class="page-title">🎬 Movies</h1>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert border-0 mb-4" style="background-color: rgba(25,135,84,0.2); color: #75e0a7;" id="successAlert">
                ✅
                <?php
                if ($_GET['success'] === 'added') echo 'Film berhasil ditambahkan!';
                if ($_GET['success'] === 'updated') echo 'Film berhasil diupdate!';
                if ($_GET['success'] === 'deleted') echo 'Film berhasil dihapus!';
                ?>
            </div>
        <?php endif; ?>

        <!-- Form Tambah / Edit -->
        <div class="card-form">
            <h5 style="color: var(--accent-light); margin-bottom: 20px;">
                <?= $edit_movie ? '✏️ Edit Movie' : '➕ Add New Movie' ?>
            </h5>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="<?= $edit_movie ? 'edit' : 'add' ?>">
                <?php if ($edit_movie): ?>
                    <input type="hidden" name="movie_id" value="<?= $edit_movie['movie_id'] ?>">
                <?php endif; ?>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" placeholder="Movie title" value="<?= $edit_movie['title'] ?? '' ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Genre</label>
                        <select name="genre" class="form-select" required>
                            <option value="">Select genre</option>
                            <?php
                            $genres = ['Action', 'Adventure', 'Animation', 'Comedy', 'Drama', 'Horror', 'Romance', 'Sci-Fi', 'Thriller'];
                            foreach ($genres as $g):
                            ?>
                                <option value="<?= $g ?>" <?= ($edit_movie['genre'] ?? '') === $g ? 'selected' : '' ?>><?= $g ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Duration (minutes)</label>
                        <input type="number" name="duration" class="form-control" placeholder="120" value="<?= $edit_movie['duration'] ?? '' ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Synopsis</label>
                        <textarea name="synopsis" class="form-control" rows="3" placeholder="Movie synopsis"><?= $edit_movie['synopsis'] ?? '' ?></textarea>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Release Date</label>
                        <input type="date" name="release_date" class="form-control" value="<?= $edit_movie['release_date'] ?? '' ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Poster <?= $edit_movie ? '(kosongkan jika tidak diganti)' : '' ?></label>
                        <input type="file" name="poster" class="form-control" accept="image/*">
                    </div>
                </div>

                <div class="mt-3 d-flex gap-2">
                    <button type="submit" class="btn-submit">
                        <?= $edit_movie ? '💾 Update Movie' : '➕ Add Movie' ?>
                    </button>
                    <?php if ($edit_movie): ?>
                        <a href="movies.php" class="btn-edit" style="padding: 10px 24px;">✖ Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Tabel Film -->
        <div style="background: linear-gradient(145deg, #804A8A33, #3A035333); border-radius: 12px; overflow: hidden;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Poster</th>
                        <th>Title</th>
                        <th>Genre</th>
                        <th>Duration</th>
                        <th>Release Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($movies as $movie): ?>
                        <tr>
                            <td>
                                <?php if ($movie['poster'] && file_exists('../assets/img/' . $movie['poster'])): ?>
                                    <img src="/e-ticket_cinema/assets/img/<?= $movie['poster'] ?>" class="movie-poster-thumb">
                                <?php else: ?>
                                    <div class="no-poster-thumb">🎬</div>
                                <?php endif; ?>
                            </td>
                            <td><?= $movie['title'] ?></td>
                            <td><span class="badge-genre"><?= $movie['genre'] ?></span></td>
                            <td><?= $movie['duration'] ?> min</td>
                            <td><?= $movie['release_date'] ?></td>
                            <td class="d-flex gap-2">
                                <a href="movies.php?edit=<?= $movie['movie_id'] ?>" class="btn-edit">✏️ Edit</a>
                                <a href="movies.php?delete=<?= $movie['movie_id'] ?>" class="btn-delete" onclick="return confirm('Hapus film ini?')">🗑️ Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($movies)): ?>
                        <tr>
                            <td colspan="6" class="text-center" style="color: rgba(255,255,255,0.4); padding: 30px;">No movies yet</td>
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