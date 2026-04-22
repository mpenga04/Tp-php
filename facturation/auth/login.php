<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/includes/fonctions-auth.php';
require_once dirname(__DIR__) . '/auth/session.php';

if (current_user()) {
    header('Location: /Tp php/facturation/index.php');
    exit;
}

$identifiant = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifiant = trim((string) ($_POST['identifiant'] ?? ''));
    $password = (string) ($_POST['mot_de_passe'] ?? '');
    $user = authenticate_user($identifiant, $password);
    if ($user) {
        $_SESSION['user'] = $user;
        header('Location: /Tp php/facturation/index.php');
        exit;
    }
    $error = 'Identifiants invalides ou compte inactif.';
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link rel="stylesheet" href="/Tp php/facturation/assets/css/style.css">
</head>
<body>
<main class="container">
    <section class="card">
        <h1>Connexion</h1>
        <?php if ($error): ?><div class="alert error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <form method="post">
            <label>Identifiant <input name="identifiant" value="<?= htmlspecialchars($identifiant) ?>" required></label>
            <label>Mot de passe <input type="password" name="mot_de_passe" required></label>
            <button type="submit">Se connecter</button>
        </form>
    </section>
</main>
</body>
</html>
