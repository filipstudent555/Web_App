<?php
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function validate_movie(array $data): array
{
    $errors = [];

    $title = trim($data['title'] ?? '');
    $genre = trim($data['genre'] ?? '');
    $country = trim($data['country'] ?? '');
    $release_year = (int)($data['release_year'] ?? 0);
    $duration_min = (int)($data['duration_min'] ?? 0);
    $rating = (float)($data['rating'] ?? -1);

    if ($title === '') {
        $errors[] = 'Naslov filma je obavezan.';
    }

    if ($genre === '') {
        $errors[] = 'Žanr filma je obavezan.';
    }

    if ($country === '') {
        $errors[] = 'Zemlja je obavezna.';
    }

    $current_year = (int)date('Y') + 1;

    if ($release_year < 1888 || $release_year > $current_year) {
        $errors[] = "Godina mora biti između 1888 i {$current_year}.";
    }

    if ($duration_min < 1 || $duration_min > 500) {
        $errors[] = 'Trajanje filma mora biti između 1 i 500 minuta.';
    }

    if ($rating < 0 || $rating > 10) {
        $errors[] = 'Ocjena mora biti između 0.0 i 10.0.';
    }

    return $errors;
}

function redirect_with_message(string $url, string $message, string $type = 'success'): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;

    header("Location: {$url}");
    exit;
}

function display_flash_message(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!empty($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'] ?? 'success';

        $class = $type === 'error' ? 'alert alert-error' : 'alert alert-success';

        echo '<div class="' . e($class) . '">' . e($message) . '</div>';

        unset($_SESSION['flash_message'], $_SESSION['flash_type']);
    }
}