<?php
require_once 'config/db.php';
include 'includes/header.php';

$stmt = $pdo->query("SELECT * FROM movies ORDER BY release_date DESC");
$movies = $stmt->fetchAll();
?>

<div class="container py-5">
    <div class="text-center mb-5">
        <h1 style="background: linear-gradient(90deg, #F8D299, #F59E51); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-weight: 800; font-size: 2.5rem;">
            Now Showing 🎬
        </h1>
        <p style="color: rgba(255,255,255,0.5);">Book your favorite movie tickets easily and quickly</p>
    </div>

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
                            <span class="badge-genre"><?= $movie['genre'] ?></span>
                            <p class="movie-duration">⏱ <?= $movie['duration'] ?> min</p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <p style="color: rgba(255,255,255,0.4); font-size: 1.2rem;">No movies available at the moment.</p>
        </div>
    <?php endif; ?>
</div>

<style>
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
        top: 0; left: 0;
        width: 100%; height: 100%;
        object-fit: cover;
    }
    .no-poster {
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 4rem;
        background: linear-gradient(145deg, #3A0353, #1a0230);
    }
    .movie-overlay {
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(26,2,48,0.85);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s;
    }
    .movie-card:hover .movie-overlay { opacity: 1; }
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
    .btn-buy:hover { opacity: 0.85; color: #3A0353; }
    .movie-info { padding: 12px; }
    .movie-title {
        color: white;
        font-weight: 700;
        margin-bottom: 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .movie-duration {
        color: rgba(255,255,255,0.5);
        font-size: 0.8rem;
        margin-top: 6px;
        margin-bottom: 0;
    }
</style>

<?php include 'includes/footer.php'; ?>