<?php
$serveur = "localhost";
$utilisateur = "root";
$motDePasse = "";
$base = "GestForma";

try {
    $dsn = "mysql:host=$serveur;dbname=$base;charset=utf8mb4";
    $connexion = new PDO($dsn, $utilisateur, $motDePasse, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    echo "Connexion à la base réussie.<br>";

    $requete = "SELECT COUNT(*) AS total FROM utilisateurs";
    $total = $connexion->query($requete)->fetchColumn();

    echo "Nombre d'utilisateurs : " . $total;
} catch (PDOException $exception) {
    die("Échec de la connexion ou de la requête : " . $exception->getMessage());
}
?>