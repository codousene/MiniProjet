<?php
$dossierPublic = "http://localhost/MiniProjet/public/";

try {
    $db = new PDO('mysql:host=localhost;dbname=taches_employe;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die('Erreur de connexion : ' . $e->getMessage());
}

require_once "traitements/requete.php"; 
include_once "includes/header.php";
include_once "includes/navbar.php";
include_once "includes/sidebar.php";

// 4. Routage
$page = isset($_GET['page']) ? $_GET['page'] : "acceuil";

if (file_exists("pages/$page.php")) {
    include_once "pages/$page.php"; 
} else {
    include_once "pages/erreur404.php";
}

include_once "includes/footer.php";
?>