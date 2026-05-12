<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

$page_title = 'Filmovi';

$genre = trim($_GET['genre'] ?? '');
$year = trim($_GET['year'] ?? '');
$country = trim($_GET['country'] ?? '');
$search = trim($_GET['search'] ?? '');
$sort = $_GET['sort'] ?? 'title';
$direction = $_GET['direction'] ?? 'asc';

$allowed_sort_columns = [
    'title' => 'title',
    'genre' => 'genre',
    'country' => 'country',
    'release_year' => 'release_year',
    'duration_min' => 'duration_min',
    'rating' => 'rating'
];

$allowed_directions = ['asc', 'desc'];

$sort_column = $allowed_sort_columns[$sort] ?? 'title';
$sort_direction = in_array(strtolower($direction), $allowed_directions, true)
    ? strtoupper($direction)
    : 'ASC';

$where = [];
$params = [];

if ($genre !== '') {
    $where[] = 'genre LIKE :genre';
    $params[':genre'] = '%' . $genre . '%';
}

if ($year !== '') {
    $where[] = 'release_year = :year';
    $params[':year'] = (int)$year;
}

if ($country !== '') {
    $where[] = 'country = :country';
    $params[':country'] = $country;
}

if ($search !== '') {
    $where[] = 'title LIKE :search';
    $params[':search'] = '%' . $search . '%';
}

$where_sql = '';

if (!empty($where)) {
    $where_sql = 'WHERE ' . implode(' AND ', $where);
}

$sql = "
    SELECT *
    FROM movies
    {$where_sql}
    ORDER BY {$sort_column} {$sort_direction}
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$movies = $stmt->fetchAll();

$genres = $pdo->query("
    SELECT DISTINCT genre 
    FROM movies 
    ORDER BY genre ASC
")->fetchAll();

$years = $pdo->query("
    SELECT DISTINCT release_year 
    FROM movies 
    ORDER BY release_year DESC
")->fetchAll();

$countries = $pdo->query("
    SELECT DISTINCT country 
    FROM movies 
    ORDER BY country ASC
")->fetchAll();

require_once 'includes/header.php';
?>

<section class="intro">
    <article>
        <h2>Pregled filmova</h2>
        <p>
            Ovdje se filmovi dohvaćaju iz MySQL baze. Filtriranje, pretraživanje i sortiranje rade se serverski putem SQL upita.
        </p>
    </article>
</section>

<section class="table-card">
    <h2>Filtri</h2>

    <form method="get" class="filteri">
        <label>
            Pretraži naslov:
            <input type="text" name="search" value="<?= e($search) ?>" placeholder="npr. Extraction">
        </label>

        <label>
            Žanr:
            <select name="genre">
                <option value="">Svi žanrovi</option>
                <?php foreach ($genres as $g): ?>
                    <option value="<?= e($g['genre']) ?>" <?= $genre === $g['genre'] ? 'selected' : '' ?>>
                        <?= e($g['genre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>
            Godina:
            <select name="year">
                <option value="">Sve godine</option>
                <?php foreach ($years as $y): ?>
                    <option value="<?= e($y['release_year']) ?>" <?= $year == $y['release_year'] ? 'selected' : '' ?>>
                        <?= e($y['release_year']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>
            Zemlja:
            <select name="country">
                <option value="">Sve zemlje</option>
                <?php foreach ($countries as $c): ?>
                    <option value="<?= e($c['country']) ?>" <?= $country === $c['country'] ? 'selected' : '' ?>>
                        <?= e($c['country']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>
            Sortiraj po:
            <select name="sort">
                <option value="title" <?= $sort === 'title' ? 'selected' : '' ?>>Naslov</option>
                <option value="genre" <?= $sort === 'genre' ? 'selected' : '' ?>>Žanr</option>
                <option value="country" <?= $sort === 'country' ? 'selected' : '' ?>>Zemlja</option>
                <option value="release_year" <?= $sort === 'release_year' ? 'selected' : '' ?>>Godina</option>
                <option value="duration_min" <?= $sort === 'duration_min' ? 'selected' : '' ?>>Trajanje</option>
                <option value="rating" <?= $sort === 'rating' ? 'selected' : '' ?>>Ocjena</option>
            </select>
        </label>

        <label>
            Smjer:
            <select name="direction">
                <option value="asc" <?= strtolower($direction) === 'asc' ? 'selected' : '' ?>>Uzlazno</option>
                <option value="desc" <?= strtolower($direction) === 'desc' ? 'selected' : '' ?>>Silazno</option>
            </select>
        </label>

        <button type="submit">Primijeni filtre</button>
        <a class="button-link" href="filmovi.php">Resetiraj</a>
    </form>
</section>

<section class="table-card">
    <h2>Tablica filmova</h2>

    <div class="table-wrap">
        <table>
            <caption>Podaci o filmovima</caption>
            <thead>
                <tr>
                    <th>Naslov</th>
                    <th>Žanr</th>
                    <th>Zemlja</th>
                    <th>Godina</th>
                    <th>Trajanje</th>
                    <th>Ocjena</th>
                    <th>Akcija</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($movies)): ?>
                    <tr>
                        <td colspan="7">Nema filmova za odabrane filtre.</td>
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
                            <td>
                                <?php if (is_logged_in()): ?>
                                    <form method="post" action="dodaj_u_videoteku.php">
                                        <input type="hidden" name="movie_id" value="<?= (int)$movie['id'] ?>">
                                        <button type="submit">Dodaj</button>
                                    </form>
                                <?php else: ?>
                                    <a href="login.php">Prijavi se</a>
                                <?php endif; ?>
                            </td>
                        </tr>

                        <?php if ((float)$movie['rating'] < 5.0): ?>
                            <tr>
                                <td colspan="7">
                                    <div class="alert alert-error">
                                        Upozorenje: film "<?= e($movie['title']) ?>" ima nisku prosječnu ocjenu
                                        <?= e(number_format((float)$movie['rating'], 1)) ?>.
                                        Razmislite prije dodavanja u osobnu videoteku.
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