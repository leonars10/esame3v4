<?php
//$servername = "localhost"; // O l'IP del tuo server MySQL
//$username = "root";        // Il tuo username del database
//$password = "";            // La tua password del database
//$dbname = "attivita_db";   // Il nome del database che hai creato

$servername = 'localhost';
$dbname   = 'LCWEB_PORTFOLIO';
$username = 'LCWEB_BACKEND01';
$password = 'dcg_Rfv643!fvTb(6369_D'; // Usa un file .env in produzione

// Creazione della connessione
$conn = new mysqli($servername, $username, $password, $dbname);

// Controllo della connessione
if ($conn->connect_error) {
    die("Connessione al database fallita: " . $conn->connect_error);
}
?>
