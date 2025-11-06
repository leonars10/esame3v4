<?php
$host = 'localhost';
$db   = 'LCWEB_PORTFOLIO';
$user = 'LCWEB_FRONTEND01';
$pass = 'dcg_RfvDFv(fg45$fbjt74)'; // Usa un file .env in produzione

$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (Exception $e) {
    die("DB Connection failed: " . $e->getMessage());
}
?>
