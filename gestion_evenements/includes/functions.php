<?php
function verifier_connexion() {
    if (!isset($_SESSION['id_annonceur'])) {
        header('Location: /gestion_evenements/login.php');
        exit();
    }
}
?>
