<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

require_admin();

$page_title = 'Uredi film';
$errors = [];

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    redirect_with_message('dashboard.php', 'Neispravan ID filma.', 'error');
}

$stmt = $pdo->prepare("
    SELECT *
    FROM movies
    WHERE id = :id
    LIMIT 1
");
$stmt->execute([':id' => $id]);
$movie = $stmt->fetch();

if (!$movie) {
    redirect_with_message('dashboard.php', 'Film ne postoji.', 'error');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = validate_movie($_POST);

    if (empty($errors)) {
        $stmt = $pdo->prepare("
            UPDATE movies
            SET
                title = :title,
                genre = :genre,
                country = :country,
                release_year = :release_year,
                duration_min = :duration_min,
                rating = :rating
            WHERE id = :id
        ");

        $stmt->execute([
            ':title' => trim($_POST['title']),
            ':genre' => trim($_POST['genre']),
            ':country' => trim($_POST['country']),
            ':release_year' => (int)$_POST['release_year'],
            ':duration_min' => (int)$_POST['duration_min'],
            ':rating' => (float)$_POST['rating'],
            ':id' => $id
        ]);

        redirect_with_message('dashboard.php', 'Film je uspješno uređen.');
    }
}

require_once '../includes/header.php';
?>

<section class="intro">
    <article>
        <h2>Uredi film</h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <?php foreach ($errors as $error): ?>
                    <p><?= e($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post" class="form-card">
            <label>
                Naslov:
                <input 
                    type="text" 
                    name="title" 
                    value="<?= e($_POST['title'] ?? $movie['title']) ?>" 
                    required
                >
            </label>

            <label>
                Žanr:
                <input 
                    type="text" 
                    name="genre" 
                    value="<?= e($_POST['genre'] ?? $movie['genre']) ?>" 
                    required
                >
            </label>

            <label>
                Zemlja:
                <input 
                    type="text" 
                    name="country" 
                    value="<?= e($_POST['country'] ?? $movie['country']) ?>" 
                    required
                >
            </label>

            <label>
                Godina:
                <input 
                    type="number" 
                    name="release_year" 
                    value="<?= e($_POST['release_year'] ?? $movie['release_year']) ?>" 
                    required
                >
            </label>

            <label>
                Trajanje u minutama:
                <input 
                    type="number" 
                    name="duration_min" 
                    value="<?= e($_POST['duration_min'] ?? $movie['duration_min']) ?>" 
                    required
                >
            </label>

            <label>
                Ocjena:
                <input 
                    type="number" 
                    name="rating" 
                    step="0.1" 
                    min="0" 
                    max="10" 
                    value="<?= e($_POST['rating'] ?? $movie['rating']) ?>" 
                    required
                >
            </label>

            <button type="submit">Spremi promjene</button>
            <a class="button-link" href="dashboard.php">Odustani</a>
        </form>
    </article>
</section>

<?php require_once '../includes/footer.php'; ?>