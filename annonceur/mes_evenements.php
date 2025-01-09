<?php
session_start();
if (!isset($_SESSION['id_annonceur']) || $_SESSION['role_annonceur'] != 'annonceur') {
    header('Location: ../login.php');
    exit();
}
?>
