<?php
function getUsers() {
    global $db;
    $query = $db->query("SELECT * FROM utilisateur ORDER BY id ASC");
    return $query->fetchAll(PDO::FETCH_ASSOC);
}
function getTaches() {
    global $db;
    $query = $db->query("SELECT * FROM tache ORDER BY id ASC");
    return $query->fetchAll(PDO::FETCH_ASSOC);
}
?>