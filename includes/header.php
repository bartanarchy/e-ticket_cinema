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

        .navbar {
            background: linear-gradient(90deg, var(--dark), var(--primary));
            border-bottom: 1px solid rgba(245,158,81,0.2);
            padding: 12px 0;
        }

        .navbar-brand {
            background: linear-gradient(90deg, #F8D299, #F59E51);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 700;
            font-size: 1.5rem;
        }

        .nav-link {
            color: rgba(255,255,255,0.7) !important;
            font-weight: 500;
            transition: color 0.2s;
        }

        .nav-link:hover {
            color: var(--accent) !important;
        }

        .btn-login-nav {
            background: linear-gradient(90deg, #F8D299, #F59E51);
            border: none;
            color: var(--primary);
            font-weight: 700;
            border-radius: 8px;
            padding: 6px 20px;
            text-decoration: none;
            transition: opacity 0.2s;
        }

        .btn-login-nav:hover {
            opacity: 0.85;
            color: var(--primary);
        }

        .navbar-toggler {
            border-color: rgba(245,158,81,0.5);
        }

        .dropdown-menu {
            background-color: var(--primary);
            border: 1px solid rgba(245,158,81,0.2);
        }

        .dropdown-item {
            color: rgba(255,255,255,0.8);
        }

        .dropdown-item:hover {
            background-color: var(--secondary);
            color: var(--accent);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="../index.php">🎬 CinemaTicket</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../movies.php">Movies</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto align-items-center">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                👤 <?= $_SESSION['name'] ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="../my_tickets.php">🎟️ My Tickets</a></li>
                                <?php if ($_SESSION['role'] === 'admin'): ?>
                                    <li><a class="dropdown-item" href="../admin/index.php">⚙️ Admin Panel</a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider" style="border-color: rgba(245,158,81,0.2);"></li>
                                <li><a class="dropdown-item text-danger" href="../logout.php">🚪 Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item me-2">
                            <a class="nav-link" href="../login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn-login-nav" href="../register.php">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>