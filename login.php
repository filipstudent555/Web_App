<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

$page_title = 'Prijava';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($login === '') {
        $errors[] = 'Unesite korisničko ime ili email.';
    }

    if ($password === '') {
        $errors[] = 'Unesite lozinku.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("
            SELECT *
            FROM users
            WHERE username = :login_username OR email = :login_email
            LIMIT 1
        ");

        $stmt->execute([
            ':login_username' => $login,
            ':login_email' => $login
        ]);

        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $errors[] = 'Neispravni podaci za prijavu.';
        } else {
            session_regenerate_id(true);

            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            redirect_with_message('index.php', 'Uspješno ste prijavljeni.');
        }
    }
}

require_once 'includes/header.php';
?>

<section class="intro">
    <article>
        <h2>Prijava korisnika</h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <?php foreach ($errors as $error): ?>
                    <p><?= e($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post" class="form-card">
            <label>
                Korisničko ime ili email:
                <input type="text" name="login" value="<?= e($_POST['login'] ?? '') ?>" required>
            </label>

            <label>
                Lozinka:
                <input type="password" name="password" required>
            </label>

            <button type="submit">Prijavi se</button>
        </form>

        <p>
            Nemaš račun?
            <a href="register.php">Registriraj se</a>
        </p>
    </article>
</section>

<?php require_once 'includes/footer.php'; ?>