<?php
session_start();
require_once 'config/db.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$movie_id = $_GET['id'];

// GET detail film
$stmt = $pdo->prepare("SELECT * FROM movies WHERE movie_id = ?");
$stmt->execute([$movie_id]);
$movie = $stmt->fetch();

if (!$movie) {
    header('Location: index.php');
    exit;
}

// GET jadwal tayang film ini
$stmt = $pdo->prepare("
    SELECT showtimes.*, 
    COUNT(tickets.ticket_id) as booked_seats
    FROM showtimes
    LEFT JOIN tickets ON showtimes.showtime_id = tickets.showtime_id 
    AND tickets.status = 'booked'
    WHERE showtimes.movie_id = ? 
    AND showtimes.date >= CURDATE()
    GROUP BY showtimes.showtime_id
    ORDER BY showtimes.date ASC, showtimes.time ASC
");
$stmt->execute([$movie_id]);
$showtimes = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row g-5">
        <!-- Poster -->
        <div class="col-md-3">
            <div class="poster-wrapper">
                <?php if ($movie['poster'] && file_exists('assets/img/' . $movie['poster'])): ?>
                    <img src="assets/img/<?= $movie['poster'] ?>" alt="<?= $movie['title'] ?>" class="poster-img">
                <?php else: ?>
                    <div class="no-poster-large">🎬</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Info Film -->
        <div class="col-md-9">
            <h1 class="movie-title-large"><?= $movie['title'] ?></h1>
            <div class="d-flex gap-2 mb-3 flex-wrap">
                <span class="badge-info">🎭 <?= $movie['genre'] ?></span>
                <span class="badge-info">⏱ <?= $movie['duration'] ?> min</span>
                <span class="badge-info">📅 <?= date('d M Y', strtotime($movie['release_date'])) ?></span>
            </div>

            <?php if ($movie['synopsis']): ?>
                <h5 style="color: var(--accent-light); margin-bottom: 10px;">Synopsis</h5>
                <p style="color: rgba(255,255,255,0.7); line-height: 1.8;"><?= $movie['synopsis'] ?></p>
            <?php endif; ?>

            <!-- Jadwal Tayang -->
            <h5 style="color: var(--accent-light); margin-top: 30px; margin-bottom: 15px;">🕐 Available Showtimes</h5>

            <?php if (count($showtimes) > 0): ?>
                <?php
                // Group by date
                $grouped = [];
                foreach ($showtimes as $show) {
                    $grouped[$show['date']][] = $show;
                }
                ?>
                <?php foreach ($grouped as $date => $shows): ?>
                    <div class="date-group mb-3">
                        <p class="date-label">📅 <?= date('l, d M Y', strtotime($date)) ?></p>
                        <div class="d-flex gap-2 flex-wrap">
                            <?php foreach ($shows as $show): ?>
                                <?php
                                // Hitung total kursi per hall (12 kursi per hall)
                                $total_seats = 12;
                                $available = $total_seats - $show['booked_seats'];
                                ?>
                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <a href="booking.php?showtime_id=<?= $show['showtime_id'] ?>" 
                                       class="showtime-btn <?= $available <= 0 ? 'showtime-full' : '' ?>">
                                        <span class="show-time"><?= date('H:i', strtotime($show['time'])) ?></span>
                                        <span class="show-hall">Hall <?= $show['hall_number'] ?></span>
                                        <span class="show-price">Rp <?= number_format($show['price'], 0, ',', '.') ?></span>
                                        <span class="show-seats <?= $available <= 3 ? 'seats-low' : '' ?>">
                                            <?= $available <= 0 ? 'Full' : $available . ' seats left' ?>
                                        </span>
                                    </a>
                                <?php else: ?>
                                    <a href="login.php" class="showtime-btn">
                                        <span class="show-time"><?= date('H:i', strtotime($show['time'])) ?></span>
                                        <span class="show-hall">Hall <?= $show['hall_number'] ?></span>
                                        <span class="show-price">Rp <?= number_format($show['price'], 0, ',', '.') ?></span>
                                        <span class="show-seats">Login to book</span>
                                    </a>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color: rgba(255,255,255,0.4);">No showtimes available for this movie.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    :root {
        --primary: #3A0353;
        --secondary: #804A8A;
        --accent: #F59E51;
        --accent-light: #F8D299;
        --dark: #1a0230;
    }

    .poster-wrapper {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 8px 32px rgba(0,0,0,0.4);
    }

    .poster-img {
        width: 100%;
        border-radius: 12px;
    }

    .no-poster-large {
        width: 100%;
        padding-top: 150%;
        background: linear-gradient(145deg, #3A0353, #1a0230);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 5rem;
        position: relative;
    }

    .movie-title-large {
        background: linear-gradient(90deg, #F8D299, #F59E51);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-weight: 800;
        font-size: 2.2rem;
        margin-bottom: 15px;
    }

    .badge-info {
        background: rgba(245,158,81,0.15);
        color: var(--accent);
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .date-label {
        color: var(--accent-light);
        font-weight: 600;
        margin-bottom: 10px;
    }

    .showtime-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        background: linear-gradient(145deg, #804A8A33, #3A035366);
        border: 1px solid rgba(245,158,81,0.3);
        border-radius: 10px;
        padding: 12px 16px;
        text-decoration: none;
        transition: all 0.2s;
        min-width: 110px;
    }

    .showtime-btn:hover {
        background: linear-gradient(145deg, #804A8A66, #3A035399);
        border-color: var(--accent);
        transform: translateY(-2px);
    }

    .showtime-full {
        opacity: 0.4;
        pointer-events: none;
    }

    .show-time {
        color: white;
        font-weight: 700;
        font-size: 1.1rem;
    }

    .show-hall {
        color: rgba(255,255,255,0.5);
        font-size: 0.75rem;
        margin-top: 2px;
    }

    .show-price {
        color: var(--accent);
        font-weight: 600;
        font-size: 0.85rem;
        margin-top: 4px;
    }

    .show-seats {
        color: rgba(255,255,255,0.5);
        font-size: 0.75rem;
        margin-top: 2px;
    }

    .seats-low {
        color: #ff6b7a !important;
    }
</style>

<?php include 'includes/footer.php'; ?>