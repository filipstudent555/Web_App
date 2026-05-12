<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

$page_title = 'Registracija';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if ($username === '') {
        $errors[] = 'Korisničko ime je obavezno.';
    } elseif (strlen($username) < 3) {
        $errors[] = 'Korisničko ime mora imati barem 3 znaka.';
    }

    if ($email === '') {
        $errors[] = 'Email je obavezan.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email nije u ispravnom formatu.';
    }

    if ($password === '') {
        $errors[] = 'Lozinka je obavezna.';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Lozinka mora imati barem 6 znakova.';
    }

    if ($password !== $password_confirm) {
        $errors[] = 'Lozinke se ne podudaraju.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("
            SELECT id 
            FROM users 
            WHERE username = :username OR email = :email
            LIMIT 1
        ");
        $stmt->execute([
            ':username' => $username,
            ':email' => $email
        ]);

        if ($stmt->fetch()) {
            $errors[] = 'Korisničko ime ili email već postoji.';
        }
    }

    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("
            INSERT INTO users (username, email, password_hash, role)
            VALUES (:username, :email, :password_hash, 'user')
        ");

        $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':password_hash' => $password_hash
        ]);

        redirect_with_message('login.php', 'Registracija je uspješna. Sada se možete prijaviti.');
    }
}

require_once 'includes/header.php';
?>

<section class="intro">
    <article>
        <h2>Registracija korisnika</h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <?php foreach ($errors as $error): ?>
                    <p><?= e($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post" class="form-card">
            <label>
                Korisničko ime:
                <input type="text" name="username" value="<?= e($_POST['username'] ?? '') ?>" required>
            </label>

            <label>
                Email:
                <input type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" required>
            </label>

            <label>
                Lozinka:
                <input type="password" name="password" required>
            </label>

            <label>
                Ponovi lozinku:
                <input type="password" name="password_confirm" required>
            </label>

            <button type="submit">Registriraj se</button>
        </form>
    </article>
</section>

<?php require_once 'includes/footer.php'; ?>