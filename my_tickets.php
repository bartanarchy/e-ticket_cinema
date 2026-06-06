<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// GET semua transaksi user ini
$stmt = $pdo->prepare("
    SELECT transactions.*, 
    COUNT(tickets.ticket_id) as total_tickets,
    movies.title as movie_title,
    movies.poster,
    showtimes.date as show_date,
    showtimes.time as show_time,
    showtimes.hall_number
    FROM transactions
    INNER JOIN tickets ON transactions.transaction_id = tickets.transaction_id
    INNER JOIN showtimes ON tickets.showtime_id = showtimes.showtime_id
    INNER JOIN movies ON showtimes.movie_id = movies.movie_id
    WHERE transactions.user_id = ?
    GROUP BY transactions.transaction_id, transactions.user_id, transactions.transaction_date, 
    transactions.total_amount, transactions.payment_method, movies.title, movies.poster, 
    showtimes.date, showtimes.time, showtimes.hall_number
    ORDER BY transactions.transaction_date DESC
");
$stmt->execute([$_SESSION['user_id']]);
$transactions = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="container py-5">
    <h1 class="page-title mb-4">🎟️ My Tickets</h1>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert border-0 mb-4" style="background-color: rgba(25,135,84,0.2); color: #75e0a7;" id="successAlert">
            ✅ Booking berhasil! Tiket kamu sudah dipesan.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['canceled'])): ?>
        <div class="alert border-0 mb-4" style="background-color: rgba(220,53,69,0.2); color: #ff6b7a;" id="cancelAlert">
            ✅ Tiket berhasil dibatalkan!
        </div>
    <?php endif; ?>

    <?php if (count($transactions) > 0): ?>
        <div class="row g-4">
            <?php foreach ($transactions as $trx): ?>
                <div class="col-12">
                    <div class="ticket-card">
                        <div class="row g-0 align-items-center">
                            <!-- Poster -->
                            <div class="col-md-1 text-center">
                                <?php if ($trx['poster'] && file_exists('assets/img/' . $trx['poster'])): ?>
                                    <img src="assets/img/<?= $trx['poster'] ?>" alt="<?= $trx['movie_title'] ?>" class="ticket-poster">
                                <?php else: ?>
                                    <div class="ticket-no-poster">🎬</div>
                                <?php endif; ?>
                            </div>

                            <!-- Info -->
                            <div class="col-md-7 px-4">
                                <h5 class="ticket-movie-title"><?= $trx['movie_title'] ?></h5>
                                <div class="d-flex gap-3 flex-wrap mt-2">
                                    <span class="ticket-info-item">📅 <?= date('d M Y', strtotime($trx['show_date'])) ?></span>
                                    <span class="ticket-info-item">🕐 <?= date('H:i', strtotime($trx['show_time'])) ?></span>
                                    <span class="ticket-info-item">🏛️ Hall <?= $trx['hall_number'] ?></span>
                                    <span class="ticket-info-item">🎟️ <?= $trx['total_tickets'] ?> ticket(s)</span>
                                </div>
                                <!-- Seat list -->
                                <?php
                                $stmt2 = $pdo->prepare("
                                    SELECT seats.seat_code, tickets.status
                                    FROM tickets
                                    INNER JOIN seats ON tickets.seat_id = seats.seat_id
                                    WHERE tickets.transaction_id = ?
                                ");
                                $stmt2->execute([$trx['transaction_id']]);
                                $ticket_seats = $stmt2->fetchAll();
                                ?>
                                <div class="mt-2 d-flex gap-1 flex-wrap">
                                    <?php foreach ($ticket_seats as $ts): ?>
                                        <span class="seat-badge <?= $ts['status'] === 'canceled' ? 'seat-canceled' : '' ?>">
                                            <?= $ts['seat_code'] ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- Payment & Total -->
                            <div class="col-md-2 text-center">
                                <span class="payment-badge">
                                    <?php
                                    if ($trx['payment_method'] === 'transfer') echo '🏦 Transfer';
                                    elseif ($trx['payment_method'] === 'cash') echo '💵 Cash';
                                    else echo '📱 E-Wallet';
                                    ?>
                                </span>
                                <p class="ticket-total mt-2">Rp <?= number_format($trx['total_amount'], 0, ',', '.') ?></p>
                                <p class="ticket-date"><?= date('d M Y, H:i', strtotime($trx['transaction_date'])) ?></p>
                            </div>

                            <!-- Cancel Button -->
                            <div class="col-md-2 text-center">
                                <?php
                                $all_booked = true;
                                foreach ($ticket_seats as $ts) {
                                    if ($ts['status'] === 'canceled') {
                                        $all_booked = false;
                                        break;
                                    }
                                }
                                ?>
                                <?php if ($all_booked): ?>
                                    <a href="cancel_ticket.php?id=<?= $trx['transaction_id'] ?>"
                                        class="btn-cancel"
                                        onclick="return confirm('Batalkan tiket ini?')">
                                        ✖ Cancel
                                    </a>
                                <?php else: ?>
                                    <span class="canceled-badge">Canceled</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <p style="font-size: 4rem;">🎟️</p>
            <p style="color: rgba(255,255,255,0.4); font-size: 1.2rem;">You haven't booked any tickets yet.</p>
            <a href="index.php" style="background: linear-gradient(90deg, #F8D299, #F59E51); color: #3A0353; padding: 10px 24px; border-radius: 8px; text-decoration: none; font-weight: 700;">
                Browse Movies
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

    .ticket-card {
        background: linear-gradient(145deg, #804A8A33, #3A035366);
        border: 1px solid rgba(245, 158, 81, 0.2);
        border-radius: 12px;
        padding: 20px;
        transition: transform 0.2s;
    }

    .ticket-card:hover {
        transform: translateY(-2px);
        border-color: rgba(245, 158, 81, 0.4);
    }

    .ticket-poster {
        width: 50px;
        height: 70px;
        object-fit: cover;
        border-radius: 6px;
    }

    .ticket-no-poster {
        width: 50px;
        height: 70px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin: 0 auto;
    }

    .ticket-movie-title {
        color: white;
        font-weight: 700;
        margin: 0;
    }

    .ticket-info-item {
        color: rgba(255, 255, 255, 0.6);
        font-size: 0.85rem;
    }

    .seat-badge {
        background: rgba(245, 158, 81, 0.2);
        color: var(--accent);
        padding: 3px 8px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .seat-canceled {
        background: rgba(220, 53, 69, 0.2);
        color: #ff6b7a;
        text-decoration: line-through;
    }

    .payment-badge {
        background: rgba(255, 255, 255, 0.1);
        color: rgba(255, 255, 255, 0.7);
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
    }

    .ticket-total {
        color: var(--accent);
        font-weight: 700;
        font-size: 1rem;
        margin: 0;
    }

    .ticket-date {
        color: rgba(255, 255, 255, 0.4);
        font-size: 0.75rem;
        margin: 0;
    }

    .btn-cancel {
        background: rgba(220, 53, 69, 0.2);
        color: #ff6b7a;
        border: 1px solid rgba(220, 53, 69, 0.3);
        border-radius: 8px;
        padding: 6px 16px;
        text-decoration: none;
        font-size: 0.85rem;
        transition: all 0.2s;
    }

    .btn-cancel:hover {
        background: rgba(220, 53, 69, 0.4);
        color: #ff6b7a;
    }

    .canceled-badge {
        background: rgba(220, 53, 69, 0.1);
        color: rgba(255, 100, 120, 0.5);
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
    }
</style>

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

<?php include 'includes/footer.php'; ?>