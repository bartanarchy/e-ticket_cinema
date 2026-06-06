<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: my_tickets.php');
    exit;
}

$transaction_id = $_GET['id'];

// Pastikan transaksi milik user ini
$stmt = $pdo->prepare("SELECT * FROM transactions WHERE transaction_id = ? AND user_id = ?");
$stmt->execute([$transaction_id, $_SESSION['user_id']]);
$transaction = $stmt->fetch();

if (!$transaction) {
    header('Location: my_tickets.php');
    exit;
}

// Update status tiket jadi canceled
$stmt = $pdo->prepare("UPDATE tickets SET status = 'canceled' WHERE transaction_id = ?");
$stmt->execute([$transaction_id]);

header('Location: my_tickets.php?canceled=1');
exit;
?>