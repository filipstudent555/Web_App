<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';

$page_title = $page_title ?? 'Filmoteka';
?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($page_title) ?></title>
    <link rel="stylesheet" href="public/style.css">
</head>
<body>
<header class="site-header">
    <div class="header-inner">
        <div class="branding">
            <h1><?= e($page_title) ?></h1>
        </div>

        <div class="nav-holder">
            <a href="#main-nav" class="menu-button" aria-label="Prikaži ili sakrij navigaciju">Izbornik</a>

            <nav id="main-nav" aria-label="Glavna navigacija">
                <ul class="nav-list">
                    <li><a href="index.php">Početna</a></li>
                    <li><a href="filmovi.php">Filmovi</a></li>

                    <?php if (is_logged_in()): ?>
                        <li><a href="moja_videoteka.php">Moja videoteka</a></li>

                        <?php if (is_admin()): ?>
                            <li><a href="admin/dashboard.php">Admin</a></li>
                        <?php endif; ?>

                        <li><a href="logout.php">Odjava (<?= e(current_username()) ?>)</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Prijava</a></li>
                        <li><a href="register.php">Registracija</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>
</header>

<main class="page-main">
    <?php display_flash_message(); ?>