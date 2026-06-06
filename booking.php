<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['showtime_id'])) {
    header('Location: index.php');
    exit;
}

$showtime_id = $_GET['showtime_id'];

// GET detail showtime + film
$stmt = $pdo->prepare("
    SELECT showtimes.*, movies.title, movies.poster, movies.genre, movies.duration
    FROM showtimes
    INNER JOIN movies ON showtimes.movie_id = movies.movie_id
    WHERE showtimes.showtime_id = ?
");
$stmt->execute([$showtime_id]);
$showtime = $stmt->fetch();

if (!$showtime) {
    header('Location: index.php');
    exit;
}

// GET kursi yang sudah dipesan di showtime ini
$stmt = $pdo->prepare("
    SELECT seats.seat_id FROM tickets
    INNER JOIN seats ON tickets.seat_id = seats.seat_id
    WHERE tickets.showtime_id = ? AND tickets.status = 'booked'
");
$stmt->execute([$showtime_id]);
$booked_seats = $stmt->fetchAll(PDO::FETCH_COLUMN);

// GET semua kursi di hall ini
$stmt = $pdo->prepare("SELECT * FROM seats WHERE hall_number = ? ORDER BY seat_code ASC");
$stmt->execute([$showtime['hall_number']]);
$all_seats = $stmt->fetchAll();

// PROSES BOOKING
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_seats = $_POST['seats'] ?? [];

    if (empty($selected_seats)) {
        $error = "Pilih minimal 1 kursi!";
    } else {
        // Cek apakah kursi masih tersedia
        $placeholders = implode(',', array_fill(0, count($selected_seats), '?'));
        $params = array_merge([$showtime_id], $selected_seats);
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM tickets 
            WHERE showtime_id = ? AND seat_id IN ($placeholders) AND status = 'booked'
        ");
        $stmt->execute($params);
        $already_booked = $stmt->fetchColumn();

        if ($already_booked > 0) {
            $error = "Beberapa kursi yang kamu pilih sudah dipesan orang lain!";
        } else {
            $total_amount = count($selected_seats) * $showtime['price'];
            $payment_method = $_POST['payment_method'];

            // Insert transaksi
            $stmt = $pdo->prepare("INSERT INTO transactions (user_id, total_amount, payment_method) VALUES (?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $total_amount, $payment_method]);
            $transaction_id = $pdo->lastInsertId();

            // Insert tiket per kursi
            $stmt = $pdo->prepare("INSERT INTO tickets (transaction_id, showtime_id, seat_id, status) VALUES (?, ?, ?, 'booked')");
            foreach ($selected_seats as $seat_id) {
                $stmt->execute([$transaction_id, $showtime_id, $seat_id]);
            }

            header("Location: my_tickets.php?success=1");
            exit;
        }
    }
}

include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row g-4">
        <!-- Info Film & Showtime -->
        <div class="col-md-4">
            <div class="booking-info-card">
                <?php if ($showtime['poster'] && file_exists('assets/img/' . $showtime['poster'])): ?>
                    <img src="assets/img/<?= $showtime['poster'] ?>" alt="<?= $showtime['title'] ?>" class="booking-poster">
                <?php else: ?>
                    <div class="booking-no-poster">🎬</div>
                <?php endif; ?>
                <div class="p-3">
                    <h5 style="color: white; font-weight: 700;"><?= $showtime['title'] ?></h5>
                    <p style="color: rgba(255,255,255,0.5); font-size: 0.85rem; margin-bottom: 15px;"><?= $showtime['genre'] ?> • <?= $showtime['duration'] ?> min</p>
                    <div class="showtime-detail-item">
                        <span>📅 Date</span>
                        <span><?= date('d M Y', strtotime($showtime['date'])) ?></span>
                    </div>
                    <div class="showtime-detail-item">
                        <span>🕐 Time</span>
                        <span><?= date('H:i', strtotime($showtime['time'])) ?></span>
                    </div>
                    <div class="showtime-detail-item">
                        <span>🏛️ Hall</span>
                        <span>Hall <?= $showtime['hall_number'] ?></span>
                    </div>
                    <div class="showtime-detail-item">
                        <span>💰 Price/seat</span>
                        <span style="color: var(--accent);">Rp <?= number_format($showtime['price'], 0, ',', '.') ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pilih Kursi -->
        <div class="col-md-8">
            <h4 style="background: linear-gradient(90deg, #F8D299, #F59E51); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-weight: 700; margin-bottom: 20px;">
                🎟️ Select Your Seats
            </h4>

            <?php if (isset($error)): ?>
                <div class="alert border-0 mb-3" style="background-color: rgba(220,53,69,0.2); color: #ff6b7a;">
                    ❌ <?= $error ?>
                </div>
            <?php endif; ?>

            <!-- Legend -->
            <div class="d-flex gap-3 mb-4 flex-wrap">
                <div class="d-flex align-items-center gap-2">
                    <div class="seat-legend available"></div>
                    <span style="color: rgba(255,255,255,0.6); font-size: 0.85rem;">Available</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div class="seat-legend selected"></div>
                    <span style="color: rgba(255,255,255,0.6); font-size: 0.85rem;">Selected</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div class="seat-legend booked"></div>
                    <span style="color: rgba(255,255,255,0.6); font-size: 0.85rem;">Booked</span>
                </div>
            </div>

            <!-- Screen -->
            <div class="screen-bar">SCREEN</div>

            <!-- Seat Map -->
            <form method="POST" id="bookingForm">
                <input type="hidden" name="payment_method" id="payment_method_input" value="transfer">
                <div class="seat-map">
                    <?php
                    $rows = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
                    foreach ($rows as $row):
                        for ($col = 1; $col <= 10; $col++):
                            // Cari seat yang sesuai
                            $seat = null;
                            foreach ($all_seats as $s) {
                                if ($s['seat_code'] === $row . $col) {
                                    $seat = $s;
                                    break;
                                }
                            }
                            if (!$seat) continue;
                            $is_booked = in_array($seat['seat_id'], $booked_seats);

                            // Tambah gap di tengah (setelah kolom 5)
                            if ($col === 6): ?>
                                <div class="seat-gap"></div>
                            <?php endif; ?>

                            <div class="seat <?= $is_booked ? 'seat-booked' : 'seat-available' ?>"
                                data-seat-id="<?= $seat['seat_id'] ?>"
                                data-seat-code="<?= $seat['seat_code'] ?>"
                                <?= $is_booked ? '' : 'onclick="toggleSeat(this)"' ?>>
                                <?= $seat['seat_code'] ?>
                                <?php if (!$is_booked): ?>
                                    <input type="checkbox" name="seats[]" value="<?= $seat['seat_id'] ?>" style="display:none;" class="seat-checkbox">
                                <?php endif; ?>
                            </div>
                    <?php endfor;
                    endforeach; ?>
                </div>

                <!-- Summary -->
                <div class="booking-summary mt-4">
                    <h6 style="color: var(--accent-light); margin-bottom: 15px;">📋 Booking Summary</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span style="color: rgba(255,255,255,0.6);">Selected Seats</span>
                        <span id="selected-seats-text" style="color: white;">None</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span style="color: rgba(255,255,255,0.6);">Total Price</span>
                        <span id="total-price" style="color: var(--accent); font-weight: 700;">Rp 0</span>
                    </div>

                    <!-- Payment Method -->
                    <h6 style="color: var(--accent-light); margin-bottom: 10px;">💳 Payment Method</h6>
                    <div class="d-flex gap-2 mb-4 flex-wrap">
                        <div class="payment-option active" onclick="selectPayment(this, 'transfer')">🏦 Transfer</div>
                        <div class="payment-option" onclick="selectPayment(this, 'cash')">💵 Cash</div>
                        <div class="payment-option" onclick="selectPayment(this, 'e-wallet')">📱 E-Wallet</div>
                    </div>

                    <button type="submit" class="btn-book w-100" id="bookBtn" disabled>
                        🎟️ Book Now
                    </button>
                </div>
            </form>
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

    .booking-info-card {
        background: linear-gradient(145deg, #804A8A33, #3A035366);
        border: 1px solid rgba(245, 158, 81, 0.2);
        border-radius: 12px;
        overflow: hidden;
    }

    .booking-poster {
        width: 100%;
        max-height: 300px;
        object-fit: cover;
    }

    .booking-no-poster {
        width: 100%;
        height: 200px;
        background: linear-gradient(145deg, #3A0353, #1a0230);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 4rem;
    }

    .showtime-detail-item {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        color: rgba(255, 255, 255, 0.7);
        font-size: 0.9rem;
    }

    .screen-bar {
        background: linear-gradient(90deg, transparent, rgba(245, 158, 81, 0.3), transparent);
        text-align: center;
        padding: 8px;
        color: var(--accent);
        font-size: 0.75rem;
        letter-spacing: 4px;
        margin-bottom: 20px;
        border-radius: 4px;
    }

    .seat-map {
        display: grid;
        grid-template-columns: repeat(11, 1fr);
        gap: 6px;
        margin: 0 auto 20px;
    }

    .seat-gap {
        width: 100%;
    }

    .seat {
        padding: 6px 4px;
        border-radius: 6px;
        text-align: center;
        font-size: 0.65rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
        user-select: none;
        aspect-ratio: 1;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .seat-available {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: white;
    }

    .seat-available:hover {
        background: rgba(245, 158, 81, 0.2);
        border-color: var(--accent);
    }

    .seat-selected {
        background: linear-gradient(135deg, #F8D299, #F59E51) !important;
        border-color: var(--accent) !important;
        color: var(--primary) !important;
    }

    .seat-booked {
        background: rgba(220, 53, 69, 0.2);
        border: 1px solid rgba(220, 53, 69, 0.3);
        color: rgba(255, 255, 255, 0.3);
        cursor: not-allowed;
    }

    .seat-legend {
        width: 20px;
        height: 20px;
        border-radius: 4px;
    }

    .seat-legend.available {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .seat-legend.selected {
        background: linear-gradient(135deg, #F8D299, #F59E51);
    }

    .seat-legend.booked {
        background: rgba(220, 53, 69, 0.2);
        border: 1px solid rgba(220, 53, 69, 0.3);
    }

    .booking-summary {
        background: linear-gradient(145deg, #804A8A33, #3A035366);
        border: 1px solid rgba(245, 158, 81, 0.2);
        border-radius: 12px;
        padding: 20px;
    }

    .payment-option {
        padding: 8px 16px;
        border: 1px solid rgba(245, 158, 81, 0.3);
        border-radius: 8px;
        color: rgba(255, 255, 255, 0.6);
        cursor: pointer;
        transition: all 0.2s;
        font-size: 0.85rem;
    }

    .payment-option:hover,
    .payment-option.active {
        background: rgba(245, 158, 81, 0.2);
        border-color: var(--accent);
        color: var(--accent);
    }

    .btn-book {
        background: linear-gradient(90deg, #F8D299, #F59E51);
        border: none;
        color: var(--primary);
        font-weight: 700;
        border-radius: 8px;
        padding: 12px;
        font-size: 1rem;
        transition: opacity 0.2s;
        cursor: pointer;
    }

    .btn-book:hover {
        opacity: 0.85;
    }

    .btn-book:disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }
</style>

<script>
    const price = <?= $showtime['price'] ?>;
    let selectedSeats = [];

    function toggleSeat(el) {
        const seatId = el.dataset.seatId;
        const seatCode = el.dataset.seatCode;
        const checkbox = el.querySelector('.seat-checkbox');

        if (el.classList.contains('seat-selected')) {
            el.classList.remove('seat-selected');
            el.classList.add('seat-available');
            checkbox.checked = false;
            selectedSeats = selectedSeats.filter(s => s.id !== seatId);
        } else {
            el.classList.remove('seat-available');
            el.classList.add('seat-selected');
            checkbox.checked = true;
            selectedSeats.push({
                id: seatId,
                code: seatCode
            });
        }

        updateSummary();
    }

    function updateSummary() {
        const total = selectedSeats.length * price;
        const codes = selectedSeats.map(s => s.code).join(', ');

        document.getElementById('selected-seats-text').textContent = selectedSeats.length > 0 ? codes : 'None';
        document.getElementById('total-price').textContent = 'Rp ' + total.toLocaleString('id-ID');
        document.getElementById('bookBtn').disabled = selectedSeats.length === 0;
    }

    function selectPayment(el, method) {
        document.querySelectorAll('.payment-option').forEach(o => o.classList.remove('active'));
        el.classList.add('active');
        document.getElementById('payment_method_input').value = method;
    }
</script>

<?php include 'includes/footer.php'; ?>