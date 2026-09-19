<?php

function database(): PDO
{
    static $connexion;

    if ($connexion instanceof PDO) {
        return $connexion;
    }

    $connexion = new PDO(
        'mysql:host=localhost;dbname=centreformation;charset=utf8mb4',
        'root',
        '',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

    return $connexion;
}

class Database
{
    public function getConnection(): PDO
    {
        return database();
    }
}
