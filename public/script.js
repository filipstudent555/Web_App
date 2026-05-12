let sviFilmovi = [];
let kosarica = [];

fetch('movies.csv')
    .then(response => response.text())
    .then(csv => {
        const rezultat = Papa.parse(csv, {
            header: true,
            skipEmptyLines: true
        });

        sviFilmovi = rezultat.data.map(film => ({
            naslov: film.Naslov || film['﻿Naslov'],
            zanr: film.Zanr,
            godina: Number(film.Godina),
            trajanje: Number(film.Trajanje_min),
            ocjena: Number(film.Ocjena)
        }));

        popuniFiltere(sviFilmovi);
        prikaziFilmove(sviFilmovi);
    })
    .catch(error => {
        console.error('Greška pri dohvaćanju CSV datoteke:', error);
    });

function prikaziFilmove(filmovi) {
    const tbody = document.getElementById('movies-table-body');
    tbody.innerHTML = '';

    if (filmovi.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6">Nema filmova za odabrane filtre.</td></tr>';
        return;
    }

    filmovi.forEach(film => {
        const filmJeUKosarici = kosarica.some(stavka => stavka.naslov === film.naslov);
        const red = document.createElement('tr');

        red.innerHTML = `
            <td>${film.naslov}</td>
            <td>${film.zanr}</td>
            <td>${film.godina}</td>
            <td>${film.trajanje} min</td>
            <td>${film.ocjena}</td>
            <td>
                <button 
                    type="button" 
                    class="cart-button ${filmJeUKosarici ? 'cart-button-disabled' : ''}"
                    ${filmJeUKosarici ? 'disabled' : ''}
                    aria-label="Dodaj film u košaricu">
                    🛒
                </button>
            </td>
        `;

        if (!filmJeUKosarici) {
            red.querySelector('.cart-button').addEventListener('click', () => {
                dodajUKosaricu(film);
            });
        }

        tbody.appendChild(red);
    });
}

function razdvojiZanrove(zanrovi) {
    return zanrovi
        .split(/[;,]/)
        .map(zanr => zanr.trim())
        .filter(zanr => zanr !== '');
}

function popuniSelect(idElementa, vrijednosti) {
    const select = document.getElementById(idElementa);

    vrijednosti.forEach(vrijednost => {
        const option = document.createElement('option');
        option.value = vrijednost;
        option.textContent = vrijednost;

        select.appendChild(option);
    });
}

function popuniFiltere(filmovi) {
    const sviZanrovi = filmovi.flatMap(film => razdvojiZanrove(film.zanr));
    const jedinstveniZanrovi = [...new Set(sviZanrovi)].sort();

    const jedinstveneGodine = [...new Set(filmovi.map(film => film.godina))]
        .sort((a, b) => a - b);

    const jedinstveneOcjene = [...new Set(
        filmovi
            .map(film => film.ocjena)
            .filter(ocjena => !Number.isNaN(ocjena))
    )]
    .sort((a, b) => a - b);

    popuniCheckboxFilter('genre-options', jedinstveniZanrovi, 'genre');
    popuniCheckboxFilter('year-options', jedinstveneGodine, 'year');
    popuniSelect('filter-rating', jedinstveneOcjene);
}

function popuniCheckboxFilter(containerId, vrijednosti, name) {
    const container = document.getElementById(containerId);
    container.innerHTML = '';

    vrijednosti.forEach(vrijednost => {
        const label = document.createElement('label');
        label.className = 'checkbox-option';

        label.innerHTML = `
            <input type="checkbox" name="${name}" value="${vrijednost}">
            <span>${vrijednost}</span>
        `;

        label.querySelector('input').addEventListener('change', filtrirajFilmove);
        container.appendChild(label);
    });
}

function dohvatiOdabraneCheckboxVrijednosti(name) {
    return Array.from(document.querySelectorAll(`input[name="${name}"]:checked`))
        .map(input => input.value);
}

function filtrirajFilmove() {
    const odabraniZanrovi = dohvatiOdabraneCheckboxVrijednosti('genre');
    const odabraneGodine = dohvatiOdabraneCheckboxVrijednosti('year').map(Number);
    const odabranaOcjena = document.getElementById('filter-rating').value;

    document.getElementById('genre-toggle').textContent =
        odabraniZanrovi.length === 0 ? 'Svi žanrovi' : `Odabrano: ${odabraniZanrovi.length}`;

    document.getElementById('year-toggle').textContent =
        odabraneGodine.length === 0 ? 'Sve godine' : `Odabrano: ${odabraneGodine.length}`;

    const filtriraniFilmovi = sviFilmovi.filter(film => {
        const zanroviFilma = razdvojiZanrove(film.zanr);

        const zanrMatch =
            odabraniZanrovi.length === 0 ||
            odabraniZanrovi.some(zanr => zanroviFilma.includes(zanr));

        const godinaMatch =
            odabraneGodine.length === 0 ||
            odabraneGodine.includes(film.godina);

        const ocjenaMatch =
            odabranaOcjena === '' ||
            film.ocjena === Number(odabranaOcjena);

        return zanrMatch && godinaMatch && ocjenaMatch;
    });
    prikaziFilmove(filtriraniFilmovi);
}

function resetirajFiltere() {
    document.querySelectorAll('input[name="genre"], input[name="year"]').forEach(input => {
        input.checked = false;
    });

    document.getElementById('filter-rating').value = '';
    document.getElementById('genre-toggle').textContent = 'Svi žanrovi';
    document.getElementById('year-toggle').textContent = 'Sve godine';

    prikaziFilmove(sviFilmovi);
}

document.getElementById('genre-toggle').addEventListener('click', () => {
    document.getElementById('genre-options').classList.toggle('active');
    document.getElementById('year-options').classList.remove('active');
});

document.getElementById('year-toggle').addEventListener('click', () => {
    document.getElementById('year-options').classList.toggle('active');
    document.getElementById('genre-options').classList.remove('active');
});

document.getElementById('filter-rating').addEventListener('change', filtrirajFilmove);
document.getElementById('reset-filteri').addEventListener('click', resetirajFiltere);

function dodajUKosaricu(film) {
    const vecPostoji = kosarica.some(stavka => stavka.naslov === film.naslov);

    if (vecPostoji) {
        document.getElementById('kosarica-status').textContent = 'Film je već dodan u košaricu.';
        return;
    }

    kosarica.push(film);
    prikaziKosaricu();
}

function prikaziKosaricu() {
    const tbody = document.getElementById('kosarica-body');
    const status = document.getElementById('kosarica-status');

    tbody.innerHTML = '';

    if (kosarica.length === 0) {
        status.textContent = 'Košarica je prazna.';
        prikaziFilmove(filtrirajTrenutneFilmove());
        return;
    }

    status.textContent = `U košarici je ${kosarica.length} filmova.`;

    kosarica.forEach((film, index) => {
        const red = document.createElement('tr');

        red.innerHTML = `
            <td>${film.naslov}</td>
            <td>
                <button type="button" class="table-button danger-button">Ukloni</button>
            </td>
        `;

        red.querySelector('.danger-button').addEventListener('click', () => {
            ukloniIzKosarice(index);
        });

        tbody.appendChild(red);
    });

    prikaziFilmove(filtrirajTrenutneFilmove());
}

function ukloniIzKosarice(index) {
    kosarica.splice(index, 1);
    prikaziKosaricu();
}

document.getElementById('potvrdi-posudbu').addEventListener('click', () => {
    const status = document.getElementById('kosarica-status');

    if (kosarica.length === 0) {
        status.textContent = 'Nije moguće potvrditi posudbu jer je košarica prazna.';
        return;
    }

    const brojFilmova = kosarica.length;

    kosarica = [];
    prikaziKosaricu();

    status.textContent = `Uspješno ste dodali ${brojFilmova} filma u svoju košaricu za vikend maraton!`;
});

function filtrirajTrenutneFilmove() {
    const odabraniZanrovi = dohvatiOdabraneCheckboxVrijednosti('genre');
    const odabraneGodine = dohvatiOdabraneCheckboxVrijednosti('year').map(Number);
    const odabranaOcjena = document.getElementById('filter-rating').value;

    return sviFilmovi.filter(film => {
        const zanroviFilma = razdvojiZanrove(film.zanr);

        const zanrMatch =
            odabraniZanrovi.length === 0 ||
            odabraniZanrovi.some(zanr => zanroviFilma.includes(zanr));

        const godinaMatch =
            odabraneGodine.length === 0 ||
            odabraneGodine.includes(film.godina);

        const ocjenaMatch =
            odabranaOcjena === '' ||
            film.ocjena === Number(odabranaOcjena);

        return zanrMatch && godinaMatch && ocjenaMatch;
    });
}