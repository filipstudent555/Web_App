<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: filmovi.php');
    exit;
}

$movie_id = (int)($_POST['movie_id'] ?? 0);
$user_id = current_user_id();

if ($movie_id <= 0) {
    redirect_with_message('filmovi.php', 'Neispravan film.', 'error');
}

$stmt = $pdo->prepare("
    SELECT *
    FROM movies
    WHERE id = :id
    LIMIT 1
");
$stmt->execute([':id' => $movie_id]);
$movie = $stmt->fetch();

if (!$movie) {
    redirect_with_message('filmovi.php', 'Film ne postoji.', 'error');
}

$stmt = $pdo->prepare("
    SELECT id
    FROM wanted_movies
    WHERE user_id = :user_id AND movie_id = :movie_id
    LIMIT 1
");
$stmt->execute([
    ':user_id' => $user_id,
    ':movie_id' => $movie_id
]);

if ($stmt->fetch()) {
    redirect_with_message('filmovi.php', 'Film je već dodan u vašu osobnu videoteku.', 'error');
}

$stmt = $pdo->prepare("
    INSERT INTO wanted_movies (user_id, movie_id)
    VALUES (:user_id, :movie_id)
");
$stmt->execute([
    ':user_id' => $user_id,
    ':movie_id' => $movie_id
]);

if ((float)$movie['rating'] < 5.0) {
    redirect_with_message(
        'moja_videoteka.php',
        'Film je dodan, ali ima nisku ocjenu. Provjerite želite li ga zadržati.',
        'error'
    );
}

redirect_with_message('moja_videoteka.php', 'Film je uspješno dodan u osobnu videoteku.');