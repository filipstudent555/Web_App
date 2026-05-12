<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: moja_videoteka.php');
    exit;
}

$wanted_id = (int)($_POST['wanted_id'] ?? 0);

if ($wanted_id <= 0) {
    redirect_with_message('moja_videoteka.php', 'Neispravan zapis.', 'error');
}

$stmt = $pdo->prepare("
    DELETE FROM wanted_movies
    WHERE id = :id AND user_id = :user_id
");
$stmt->execute([
    ':id' => $wanted_id,
    ':user_id' => current_user_id()
]);

redirect_with_message('moja_videoteka.php', 'Film je uklonjen iz osobne videoteke.');