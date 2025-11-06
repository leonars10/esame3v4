<?php
session_start();
require 'includes/db_backend.php'; 

$error_message = ""; // Inizializza il messaggio di errore

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Prepara la query per prevenire SQL injection
    $sql = "SELECT * FROM utenti WHERE username = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC); // Recupera i dati come array associativo

    // Verifica se l'utente esiste e la password è corretta
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $user['username'];
        // Reindirizza alla pagina principale dopo il login
        header("Location: index_FE.php");
        exit(); // Termina lo script per assicurare il reindirizzamento
    } else {
        // Imposta il messaggio di errore
        $error_message = "Nome utente o password non validi. Riprova.";
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Area Riservata Frontend</title>
    <link rel="stylesheet" href="../Css/login.min.css"> 
</head>
<body>
    <div class="login-container">
        <h2>Accedi all'Area Riservata</h2>
        <p class="subtitle">Stai entrando nell'area riservata del **BACKEND**.</p>

        <?php
        // Mostra il messaggio di errore se presente
        if (!empty($error_message)) {
            echo '<p class="error-message">' . htmlspecialchars($error_message) . '</p>';
        }
        ?>

        <form method="POST">
            <div class="form-group">
                <label for="username">Nome Utente:</label>
                <input type="text" id="username" name="username" placeholder="Inserisci il tuo username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Inserisci la tua password" required>
            </div>
            <button type="submit" class="submit-button">Accedi</button>
        </form>
    </div>
</body>
</html>