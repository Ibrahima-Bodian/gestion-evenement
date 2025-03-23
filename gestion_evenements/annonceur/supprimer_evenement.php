<?php
// Démarrer la session
session_start();

// Vérifier si l'utilisateur est connecté et est un annonceur
if (!isset($_SESSION['id_annonceur']) || $_SESSION['role_annonceur'] != 'annonceur') {
    header('Location: ../login.php');
    exit();
}

// Vérifier si l'ID de l'événement est passé en paramètre
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$id_evenement = $_GET['id'];

// Inclure la connexion à la base de données
require_once '../config/db_connect.php';

// Supprimer l'événement de la base de données
$stmt = $pdo->prepare('DELETE FROM Evenement WHERE id_evenement = ? AND id_annonceur = ?');
$stmt->execute([$id_evenement, $_SESSION['id_annonceur']]);

// Rediriger vers le tableau de bord
header('Location: index.php');
exit();
?>
