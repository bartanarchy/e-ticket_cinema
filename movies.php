<?php
require_once 'config/db.php';
include 'includes/header.php';

// Search functionality
$search = $_GET['search'] ?? '';
$genre_filter = $_GET['genre'] ?? '';

$query = "SELECT * FROM movies WHERE 1=1";
$params = [];

if ($search) {
    $query .= " AND title LIKE ?";
    $params[] = '%' . $search . '%';
}

if ($genre_filter) {
    $query .= " AND genre = ?";
    $params[] = $genre_filter;
}

$query .= " ORDER BY release_date DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$movies = $stmt->fetchAll();

// GET semua genre untuk filter
$genres = $pdo->query("SELECT DISTINCT genre FROM movies ORDER BY genre ASC")->fetchAll(PDO::FETCH_COLUMN);
?>

<div class="container py-5">
    <h1 class="page-title mb-4">🎬 All Movies</h1>

    <!-- Search & Filter -->
    <form method="GET" class="row g-3 mb-5">
        <div class="col-md-6">
            <div class="search-wrapper">
                <input type="text" name="search" class="form-control search-input" 
                       placeholder="Search movies..." value="<?= htmlspecialchars($search) ?>">
                <span class="search-icon">🔍</span>
            </div>
        </div>
        <div class="col-md-3">
            <select name="genre" class="form-select filter-select">
                <option value="">All Genres</option>
                <?php foreach ($genres as $g): ?>
                    <option value="<?= $g ?>" <?= $genre_filter === $g ? 'selected' : '' ?>><?= $g ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn-search w-100">Search</button>
        </div>
        <?php if ($search || $genre_filter): ?>
        <div class="col-md-1">
            <a href="movies.php" class="btn-clear w-100">✖</a>
        </div>
        <?php endif; ?>
    </form>

    <!-- Results count -->
    <p style="color: rgba(255,255,255,0.4); margin-bottom: 20px;">
        <?= count($movies) ?> movie(s) found
        <?= $search ? "for \"$search\"" : '' ?>
        <?= $genre_filter ? "in genre \"$genre_filter\"" : '' ?>
    </p>

    <!-- Movies Grid -->
    <?php if (count($movies) > 0): ?>
        <div class="row g-4">
            <?php foreach ($movies as $movie): ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="movie-card">
                        <div class="movie-poster">
                            <?php if ($movie['poster'] && file_exists('assets/img/' . $movie['poster'])): ?>
                                <img src="assets/img/<?= $movie['poster'] ?>" alt="<?= $movie['title'] ?>">
                            <?php else: ?>
                                <div class="no-poster">🎬</div>
                            <?php endif; ?>
                            <div class="movie-overlay">
                                <a href="detail.php?id=<?= $movie['movie_id'] ?>" class="btn-buy">
                                    🎟️ Buy Ticket
                                </a>
                            </div>
                        </div>
                        <div class="movie-info">
                            <h6 class="movie-title"><?= $movie['title'] ?></h6>
                            <span class="movie-genre"><?= $movie['genre'] ?></span>
                            <p class="movie-duration">⏱ <?= $movie['duration'] ?> min</p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <p style="font-size: 4rem;">🎬</p>
            <p style="color: rgba(255,255,255,0.4); font-size: 1.2rem;">No movies found.</p>
            <a href="movies.php" style="background: linear-gradient(90deg, #F8D299, #F59E51); color: #3A0353; padding: 10px 24px; border-radius: 8px; text-decoration: none; font-weight: 700;">
                View All Movies
            </a>
        </div>
    <?php endif; ?>
</div>

<style>
    :root {
        --primary: #3A0353;
        --secondary: #804A8A;
        --accent: #F59E51;
        --accent-light: #F8D299;
        --dark: #1a0230;
    }

    .page-title {
        background: linear-gradient(90deg, #F8D299, #F59E51);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-weight: 700;
        font-size: 1.8rem;
    }

    .search-wrapper {
        position: relative;
    }

    .search-input {
        background-color: rgba(255,255,255,0.1);
        border: 1px solid rgba(248,210,153,0.3);
        color: white;
        border-radius: 8px;
        padding-right: 40px;
    }

    .search-input::placeholder {
        color: rgba(255,255,255,0.4);
    }

    .search-input:focus {
        background-color: rgba(255,255,255,0.15);
        border-color: var(--accent);
        color: white;
        box-shadow: 0 0 0 0.2rem rgba(245,158,81,0.25);
    }

    .search-icon {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
    }

    .filter-select {
        background-color: rgba(255,255,255,0.1);
        border: 1px solid rgba(248,210,153,0.3);
        color: white;
        border-radius: 8px;
    }

    .filter-select:focus {
        background-color: rgba(255,255,255,0.15);
        border-color: var(--accent);
        color: white;
        box-shadow: 0 0 0 0.2rem rgba(245,158,81,0.25);
    }

    .filter-select option {
        background-color: var(--primary);
        color: white;
    }

    .btn-search {
        background: linear-gradient(90deg, #F8D299, #F59E51);
        border: none;
        color: var(--primary);
        font-weight: 700;
        border-radius: 8px;
        padding: 10px;
        transition: opacity 0.2s;
        cursor: pointer;
    }

    .btn-search:hover {
        opacity: 0.85;
    }

    .btn-clear {
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.2);
        color: rgba(255,255,255,0.6);
        font-weight: 700;
        border-radius: 8px;
        padding: 10px;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .btn-clear:hover {
        background: rgba(255,255,255,0.2);
        color: white;
    }

    .movie-card {
        border-radius: 12px;
        overflow: hidden;
        background: linear-gradient(145deg, #804A8A, #3A0353);
        transition: transform 0.3s, box-shadow 0.3s;
        cursor: pointer;
    }

    .movie-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 16px 40px rgba(245,158,81,0.2);
    }

    .movie-poster {
        position: relative;
        width: 100%;
        padding-top: 150%;
        overflow: hidden;
    }

    .movie-poster img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .no-poster {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 4rem;
        background: linear-gradient(145deg, #3A0353, #1a0230);
    }

    .movie-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(26,2,48,0.85);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .movie-card:hover .movie-overlay {
        opacity: 1;
    }

    .btn-buy {
        background: linear-gradient(90deg, #F8D299, #F59E51);
        color: #3A0353;
        font-weight: 700;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 0.9rem;
        transition: opacity 0.2s;
    }

    .btn-buy:hover {
        opacity: 0.85;
        color: #3A0353;
    }

    .movie-info {
        padding: 12px;
    }

    .movie-title {
        color: white;
        font-weight: 700;
        margin-bottom: 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .movie-genre {
        background: rgba(245,158,81,0.2);
        color: var(--accent);
        padding: 2px 8px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .movie-duration {
        color: rgba(255,255,255,0.5);
        font-size: 0.8rem;
        margin-top: 6px;
        margin-bottom: 0;
    }
</style>

<?php include 'includes/footer.php'; ?>