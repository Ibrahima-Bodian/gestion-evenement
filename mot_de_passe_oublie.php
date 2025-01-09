<?php
session_start();
require_once 'config/db_connect.php';
require_once 'config/config.php';

$success = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Adresse email invalide.";
    } else {
        // Vérifier si l'email existe
        $stmt = $pdo->prepare('SELECT * FROM Annonceur WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // Générer un token unique
            $reset_token = bin2hex(random_bytes(32));
            $stmt = $pdo->prepare('UPDATE Annonceur SET reset_token = ?, reset_expiry = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email = ?');
            $stmt->execute([$reset_token, $email]);

            // Créer le lien de réinitialisation
            $reset_link = BASE_URL . "reset_password.php?token=" . $reset_token;

            // Simuler un envoi d'email (afficher le lien)
            $success = "Un email a été envoyé avec un lien pour réinitialiser votre mot de passe.<br>Voici le lien pour tester : <a href='$reset_link'>$reset_link</a>";
        } else {
            $error = "Aucun compte trouvé avec cet email.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mot de Passe Oublié</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Réinitialiser votre mot de passe</h2>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php else: ?>
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="email" class="form-label">Adresse Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <button type="submit" class="btn btn-primary">Envoyer</button>
            </form>
        <?php endif; ?>
    </div>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
