<?php
// Avvia la sessione
session_start();

// Cancella tutte le variabili di sessione
$_SESSION = array();

// Se si desidera distruggere la sessione, si può fare
// in questo modo. Nota che questo distruggerà il cookie di sessione.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Distrugge la sessione sul server
session_destroy();

// Reindirizza l'utente alla pagina di login
header("Location: ../index.php");
exit;
?>