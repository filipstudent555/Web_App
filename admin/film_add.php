<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

require_admin();

$page_title = 'Dodaj film';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = validate_movie($_POST);

    if (empty($errors)) {
        $stmt = $pdo->prepare("
            INSERT INTO movies (title, genre, country, release_year, duration_min, rating)
            VALUES (:title, :genre, :country, :release_year, :duration_min, :rating)
        ");

        $stmt->execute([
            ':title' => trim($_POST['title']),
            ':genre' => trim($_POST['genre']),
            ':country' => trim($_POST['country']),
            ':release_year' => (int)$_POST['release_year'],
            ':duration_min' => (int)$_POST['duration_min'],
            ':rating' => (float)$_POST['rating']
        ]);

        redirect_with_message('dashboard.php', 'Film je uspješno dodan.');
    }
}

require_once '../includes/header.php';
?>

<section class="intro">
    <article>
        <h2>Dodaj novi film</h2>

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
                <input type="text" name="title" value="<?= e($_POST['title'] ?? '') ?>" required>
            </label>

            <label>
                Žanr:
                <input type="text" name="genre" value="<?= e($_POST['genre'] ?? '') ?>" required>
            </label>

            <label>
                Zemlja:
                <input type="text" name="country" value="<?= e($_POST['country'] ?? 'Nepoznato') ?>" required>
            </label>

            <label>
                Godina:
                <input type="number" name="release_year" value="<?= e($_POST['release_year'] ?? '') ?>" required>
            </label>

            <label>
                Trajanje u minutama:
                <input type="number" name="duration_min" value="<?= e($_POST['duration_min'] ?? '') ?>" required>
            </label>

            <label>
                Ocjena:
                <input type="number" name="rating" step="0.1" min="0" max="10" value="<?= e($_POST['rating'] ?? '') ?>" required>
            </label>

            <button type="submit">Spremi film</button>
            <a class="button-link" href="dashboard.php">Odustani</a>
        </form>
    </article>
</section>

<?php require_once '../includes/footer.php'; ?>