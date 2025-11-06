<?php
$host = 'localhost';
$db   = 'LCWEB_PORTFOLIO';
$user = 'LCWEB_BACKEND01';
$pass = 'dcg_Rfv643!fvTb(6369_D'; 

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

