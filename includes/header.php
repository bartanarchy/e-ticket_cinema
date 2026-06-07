<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CinemaTicket</title>
    <link href="/e-ticket_cinema/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/e-ticket_cinema/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="/e-ticket_cinema/index.php">🎬 CinemaTicket</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/e-ticket_cinema/index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/e-ticket_cinema/movies.php">Movies</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto align-items-center">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                👤 <?= $_SESSION['name'] ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="/e-ticket_cinema/my_tickets.php">🎟️ My Tickets</a></li>
                                <?php if ($_SESSION['role'] === 'admin'): ?>
                                    <li><a class="dropdown-item" href="/e-ticket_cinema/admin/index.php">⚙️ Admin Panel</a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider" style="border-color: rgba(245,158,81,0.2);"></li>
                                <li><a class="dropdown-item text-danger" href="/e-ticket_cinema/logout.php">🚪 Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item me-2">
                            <a class="nav-link" href="/e-ticket_cinema/login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn-login-nav" href="/e-ticket_cinema/register.php">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>