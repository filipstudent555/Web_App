<?php
$page_title = 'Filmoteka';
require_once 'includes/header.php';
?>

<section class="intro" aria-labelledby="uvod-naslov">
    <article>
        <h2 id="uvod-naslov">Dobrodošli!</h2>
        <p>
            Ovo je LV4 verzija aplikacije za Netflix filmove.
            Podaci o filmovima, korisnicima i osobnoj videoteci sada se trajno spremaju u MySQL bazu.
        </p>

        <?php if (!is_logged_in()): ?>
            <p>
                Za dodavanje filmova u osobnu videoteku potrebno je napraviti registraciju ili prijavu.
            </p>
            <p>
                <a class="button-link" href="login.php">Prijava</a>
                <a class="button-link" href="register.php">Registracija</a>
            </p>
        <?php else: ?>
            <p>
                Prijavljeni ste kao <strong><?= e(current_username()) ?></strong>.
            </p>
            <p>
                <a class="button-link" href="filmovi.php">Pregledaj filmove</a>
                <a class="button-link" href="moja_videoteka.php">Moja videoteka</a>
            </p>
        <?php endif; ?>
    </article>
</section>

<section class="content-grid">
    <article class="table-card">
        <h2>Što aplikacija podržava?</h2>

        <table>
            <thead>
                <tr>
                    <th>Funkcionalnost</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Registracija i prijava korisnika</td>
                    <td>Omogućeno</td>
                </tr>
                <tr>
                    <td>Hashiranje lozinki</td>
                    <td>Omogućeno</td>
                </tr>
                <tr>
                    <td>Prikaz filmova iz baze</td>
                    <td>Omogućeno</td>
                </tr>
                <tr>
                    <td>SQL filtriranje i sortiranje</td>
                    <td>Omogućeno</td>
                </tr>
                <tr>
                    <td>Osobna videoteka korisnika</td>
                    <td>Omogućeno</td>
                </tr>
                <tr>
                    <td>Admin upravljanje filmovima</td>
                    <td>Omogućeno za admina</td>
                </tr>
            </tbody>
        </table>
    </article>

    <aside class="info-aside">
        <h2>Preporučeni film</h2>
        <picture>
            <img
                src="https://image.tmdb.org/t/p/w500/qJ2tW6WMUDux911r6m7haRef0WH.jpg"
                alt="Preporučeni film"
                loading="lazy">
        </picture>

        <article class="mini-card">
            <h3>Napomena</h3>
            <p>
                Filmovi se sada spremaju u bazu i mogu se dodavati u osobnu videoteku nakon prijave.
            </p>
        </article>
    </aside>
</section>

<?php require_once 'includes/footer.php'; ?>