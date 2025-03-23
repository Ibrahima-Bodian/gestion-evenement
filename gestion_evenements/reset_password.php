<?php
session_start();

// Inclure la connexion à la base de données et la configuration
require_once 'config/db_connect.php';
require_once 'config/config.php';

// Initialiser les variables pour afficher des messages
$error = null;
$success = null;

// Vérifier si un token est passé dans l'URL
if (!isset($_GET['token']) || empty($_GET['token'])) {
    $error = "Token invalide ou manquant.";
} else {
    $token = $_GET['token'];

    // Si le formulaire de nouveau mot de passe est soumis
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Récupérer les mots de passe saisis
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        // Vérifier cohérence
        if (empty($password) || empty($confirm_password)) {
            $error = "Veuillez remplir tous les champs.";
        } elseif ($password !== $confirm_password) {
            $error = "Les mots de passe ne correspondent pas.";
        } else {
            // Vérifier la validité du token
            $stmt = $pdo->prepare('SELECT * FROM annonceur WHERE reset_token = ? AND reset_expiry > NOW()');
            $stmt->execute([$token]);
            $user = $stmt->fetch();

            if ($user) {
                // Hacher le nouveau mot de passe
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                // Mettre à jour le mot de passe et réinitialiser le token
                $update = $pdo->prepare('
                    UPDATE annonceur 
                    SET mot_de_passe = ?, 
                        reset_token = NULL, 
                        reset_expiry = NULL
                    WHERE id_annonceur = ?
                ');
                $update->execute([$hashed_password, $user['id_annonceur']]);

                $success = "Votre mot de passe a été réinitialisé avec succès. 
                            <a href=\"login.php\">Cliquez ici pour vous connecter</a>.";
            } else {
                $error = "Le lien est invalide ou expiré. Veuillez refaire la procédure de réinitialisation.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réinitialiser le mot de passe</title>
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
        <!-- Si pas de succès, on affiche le formulaire -->
        <?php if (!isset($error)): ?>
            <div class="alert alert-danger">
                Le lien est invalide ou le token est manquant.  
                Veuillez refaire la procédure de réinitialisation.
            </div>
        <?php else: ?>
            <!-- S'il n'y a pas d'erreur critique, on affiche le formulaire -->
            <form method="POST">
                <div class="mb-3">
                    <label for="password" class="form-label">Nouveau mot de passe</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirmer le mot de passe</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit" class="btn btn-primary">Réinitialiser</button>
            </form>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
