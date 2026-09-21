<?php

function loadDatabaseEnvironment(): void
{
    $envFile = dirname(__DIR__) . '/.env';
    if (!is_file($envFile)) {
        return;
    }

    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }

        [$name, $value] = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        if ($value !== '' && (($value[0] === '"' && substr($value, -1) === '"') || ($value[0] === "'" && substr($value, -1) === "'"))) {
            $value = substr($value, 1, -1);
        }

        if ($name !== '' && getenv($name) === false) {
            putenv($name . '=' . $value);
        }
    }
}

function databaseEnvironment(string $name, string $default): string
{
    $value = getenv($name);
    return $value === false ? $default : $value;
}

function database(): PDO
{
    static $connexion;

    if ($connexion instanceof PDO) {
        return $connexion;
    }

    loadDatabaseEnvironment();

    $host = databaseEnvironment('DB_HOST', '127.0.0.1');
    $port = databaseEnvironment('DB_PORT', '3306');
    $name = databaseEnvironment('DB_DATABASE', 'gestform');
    $user = databaseEnvironment('DB_USERNAME', 'root');
    $password = databaseEnvironment('DB_PASSWORD', '');
    $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";
    $connexion = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $connexion;
}

class Database
{
    public function getConnection(): PDO
    {
        return database();
    }
}