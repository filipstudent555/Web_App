<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

require_admin();

$page_title = 'Admin dashboard';

$stmt = $pdo->query("
    SELECT *
    FROM movies
    ORDER BY created_at DESC
");
$movies = $stmt->fetchAll();

require_once '../includes/header.php';
?>

<section class="intro">
    <article>
        <h2>Admin sučelje</h2>
        <p>
            Ovdje administrator može dodavati, uređivati i brisati filmove.
        </p>

        <p>
            <a class="button-link" href="film_add.php">Dodaj novi film</a>
        </p>
    </article>
</section>

<section class="table-card">
    <h2>Svi filmovi</h2>

    <div class="table-wrap">
        <table>
            <caption>Administracija filmova</caption>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Naslov</th>
                    <th>Žanr</th>
                    <th>Zemlja</th>
                    <th>Godina</th>
                    <th>Trajanje</th>
                    <th>Ocjena</th>
                    <th>Akcije</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($movies as $movie): ?>
                    <tr>
                        <td><?= (int)$movie['id'] ?></td>
                        <td><?= e($movie['title']) ?></td>
                        <td><?= e($movie['genre']) ?></td>
                        <td><?= e($movie['country']) ?></td>
                        <td><?= e($movie['release_year']) ?></td>
                        <td><?= e($movie['duration_min']) ?> min</td>
                        <td><?= e(number_format((float)$movie['rating'], 1)) ?></td>
                        <td>
                            <a href="film_edit.php?id=<?= (int)$movie['id'] ?>">Uredi</a>

                            <form method="post" action="film_delete.php" style="display:inline;">
                                <input type="hidden" name="id" value="<?= (int)$movie['id'] ?>">
                                <button type="submit" onclick="return confirm('Jeste li sigurni da želite obrisati film?')">
                                    Obriši
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($movies)): ?>
                    <tr>
                        <td colspan="8">Nema filmova u bazi.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>