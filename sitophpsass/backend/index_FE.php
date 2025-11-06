<?php
// Avvia la sessione PHP
session_start();

// Controllo di autenticazione
// Verifica se la sessione 'loggedin' è impostata e se è true
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Se l'utente non è autenticato, reindirizzalo alla pagina di login
    header('Location: login.php');
    exit; // Termina lo script per prevenire l'esecuzione del codice sottostante
}

// Includi il file di connessione al database
include 'includes/db_backend_msqli.php';

// Query per selezionare tutte le attività
$sql = "SELECT id, titolo, descrizione, link_pagina FROM attivita";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Le Nostre Attività</title>
    <link rel="stylesheet" href="../Css/indexfe.min.css">
</head>
<body>
    <header>
        <h1>Benvenuto nel Backend</h1>
        <p>Esplora le attività principali.</p>
        <a href="logout.php" class="logout-button">Logout</a>
     </header>

    <main class="buttons-container">
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo '<div class="activity-button">';
                echo '<h2>' . htmlspecialchars($row["titolo"]) . '</h2>';
                echo '<p>' . htmlspecialchars($row["descrizione"]) . '</p>';
                echo '<a href="' . htmlspecialchars($row["link_pagina"]) . '?id=' . $row["id"] . '" class="button-link">Scopri di più</a>';
                echo '</div>';
            }
        } else {
            echo "<p>Nessuna attività trovata.</p>";
        }
        $conn->close();
        ?>
    </main>

    <footer>
        <p>&copy; 2025 Il Tuo Sito. Tutti i diritti riservati.</p>
    </footer>

</body>
</html>