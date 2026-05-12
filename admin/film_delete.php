<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

$id = (int)($_POST['id'] ?? 0);

if ($id <= 0) {
    redirect_with_message('dashboard.php', 'Neispravan ID filma.', 'error');
}

$stmt = $pdo->prepare("
    DELETE FROM movies
    WHERE id = :id
");
$stmt->execute([':id' => $id]);

redirect_with_message('dashboard.php', 'Film je uspješno obrisan.');