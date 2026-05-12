CREATE DATABASE IF NOT EXISTS lv4_netflix
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE lv4_netflix;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(80) NOT NULL UNIQUE,
    email VARCHAR(120) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS movies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    genre VARCHAR(255) NOT NULL,
    country VARCHAR(100) NOT NULL DEFAULT 'Nepoznato',
    release_year INT NOT NULL,
    duration_min INT NOT NULL,
    rating DECIMAL(3,1) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS wanted_movies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    movie_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_wanted_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_wanted_movie
        FOREIGN KEY (movie_id) REFERENCES movies(id)
        ON DELETE CASCADE,

    CONSTRAINT unique_user_movie UNIQUE (user_id, movie_id)
);

INSERT INTO users (username, email, password_hash, role)
VALUES
(
    'admin',
    'admin@example.com',
    '$2y$10$e0NRLBSwNN.2Z21wzwXj3uFSjK3HDq9EUyXRrsXUeN4MK5fF1H0ZS',
    'admin'
);

INSERT INTO movies (title, genre, country, release_year, duration_min, rating)
VALUES
('Extraction', 'Action', 'USA', 2020, 117, 6.8),
('Bird Box', 'Drama; Horror; Sci-Fi', 'USA', 2018, 124, 6.6),
('The Irishman', 'Biography; Crime; Drama', 'USA', 2019, 209, 7.8),
('The Platform', 'Horror; Sci-Fi; Thriller', 'Spain', 2019, 94, 7.0),
('Red Notice', 'Action; Comedy; Thriller', 'USA', 2021, 118, 6.3),
('The Gray Man', 'Action; Thriller', 'USA', 2022, 129, 6.5),
('Murder Mystery', 'Comedy; Crime; Mystery', 'USA', 2019, 97, 6.0),
('Tall Girl', 'Comedy; Drama; Romance', 'USA', 2019, 101, 5.2),
('The Last Days of American Crime', 'Action; Crime; Thriller', 'USA', 2020, 148, 3.8),
('365 Days', 'Drama; Romance', 'Poland', 2020, 114, 3.3);