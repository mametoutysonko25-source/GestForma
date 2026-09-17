<?php
// Définition des variables de connexion
$serveur = "localhost";
$utilisateur = "root";
$motDePasse = "";
$base = "GestForma";

// Constantes globales de l'application
define('DB_HOST', 'localhost');
define('DB_NAME', 'GestForma');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

try {
    $dsn = "mysql:host=$serveur;dbname=$base;charset=utf8mb4";
    $connexion = new PDO($dsn, $utilisateur, $motDePasse, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);

    // Fonction globale pour récupérer l'instance PDO partout dans l'application
    function getPDO() {
        global $connexion;
        return $connexion;
    }

} catch (PDOException $exception) {
    die("Échec de la connexion ou de la requête : " . $exception->getMessage());
}
?>
