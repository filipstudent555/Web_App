<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

require_login();

$page_title = 'Moja videoteka';

$stmt = $pdo->prepare("
    SELECT 
        wm.id AS wanted_id,
        wm.created_at AS added_at,
        m.id AS movie_id,
        m.title,
        m.genre,
        m.country,
        m.release_year,
        m.duration_min,
        m.rating
    FROM wanted_movies wm
    INNER JOIN movies m ON wm.movie_id = m.id
    WHERE wm.user_id = :user_id
    ORDER BY wm.created_at DESC
");
$stmt->execute([':user_id' => current_user_id()]);
$movies = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<section class="intro">
    <article>
        <h2>Moja osobna videoteka</h2>
        <p>
            Ovdje su trajno spremljeni filmovi koje ste odabrali.
            Podaci su povezani s vašim korisničkim računom.
        </p>
    </article>
</section>

<section class="table-card">
    <h2>Odabrani filmovi</h2>

    <div class="table-wrap">
        <table>
            <caption>Moja videoteka</caption>
            <thead>
                <tr>
                    <th>Naslov</th>
                    <th>Žanr</th>
                    <th>Zemlja</th>
                    <th>Godina</th>
                    <th>Trajanje</th>
                    <th>Ocjena</th>
                    <th>Dodano</th>
                    <th>Akcija</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($movies)): ?>
                    <tr>
                        <td colspan="8">Još niste dodali nijedan film.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($movies as $movie): ?>
                        <tr>
                            <td><?= e($movie['title']) ?></td>
                            <td><?= e($movie['genre']) ?></td>
                            <td><?= e($movie['country']) ?></td>
                            <td><?= e($movie['release_year']) ?></td>
                            <td><?= e($movie['duration_min']) ?> min</td>
                            <td><?= e(number_format((float)$movie['rating'], 1)) ?></td>
                            <td><?= e($movie['added_at']) ?></td>
                            <td>
                                <form method="post" action="ukloni_iz_videoteke.php">
                                    <input type="hidden" name="wanted_id" value="<?= (int)$movie['wanted_id'] ?>">
                                    <button type="submit">Ukloni</button>
                                </form>
                            </td>
                        </tr>

                        <?php if ((float)$movie['rating'] < 5.0): ?>
                            <tr>
                                <td colspan="8">
                                    <div class="alert alert-error">
                                        Ovaj film ima nisku ocjenu. Možete ga ukloniti ako ga ne želite zadržati.
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>