<?php

$vaultAddr = getenv('VAULT_ADDR') ?: 'http://vault:8200';
$vaultToken = getenv('VAULT_TOKEN') ?: 'myroottoken';

/**
 * Récupération du secret depuis l'API KV v2 de Vault
 */
function getVaultSecret($vaultAddr, $vaultToken, $path) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "$vaultAddr/v1/secret/data/$path");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "X-Vault-Token: $vaultToken",
        "Content-Type: application/json"
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        throw new Exception("Erreur lors de la récupération du secret Vault (Code HTTP: $httpCode)");
    }

    $json = json_decode($response, true);
    return $json['data']['data'] ?? null;
}

try {
    echo "<h1>Test de connexion Vault & MySQL</h1>";

    // 1. Interroger Vault
    $dbCredentials = getVaultSecret($vaultAddr, $vaultToken, 'db');
    
    if (!$dbCredentials) {
        throw new Exception("Le secret 'db' n'a pas été trouvé dans Vault.");
    }

    echo "<p style='color:green;'>✓ Identifiants récupérés depuis Vault avec succès !</p>";

    // 2. Connexion à la base de données
    $host = $dbCredentials['host'];
    $db   = $dbCredentials['dbname'];
    $user = $dbCredentials['username'];
    $pass = $dbCredentials['password'];

    $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    echo "<p style='color:green;'>✓ Connexion à la base de données MySQL réussie !</p>";

} catch (Exception $e) {
    echo "<p style='color:red;'><strong>Erreur :</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}