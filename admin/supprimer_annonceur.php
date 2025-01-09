<?php
session_start();

// Vérifier si l'utilisateur est administrateur
if (!isset($_SESSION['id_annonceur']) || $_SESSION['role_annonceur'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

require_once '../config/db_connect.php';

// Vérifier si l'ID de l'annonceur est passé en paramètre
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_annonceur = (int)$_GET['id'];

    // Supprimer l'annonceur
    $stmt = $pdo->prepare('DELETE FROM annonceur WHERE id_annonceur = ?');
    $stmt->execute([$id_annonceur]);

    // Rediriger vers la page de gestion des annonceurs
    header('Location: gerer_annonceurs.php');
    exit();
} else {
    echo "ID invalide.";
    exit();
}
?>
