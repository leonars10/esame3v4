<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit();
}
?>

<h2>Benvenuto, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
<a href="logout.php">Logout</a>
<p>Questa è l'area riservata del sito.</p>
<p>Puoi accedere a contenuti esclusivi e gestire il tuo profilo.</p>
<p>Per ulteriori informazioni, contatta l'amministratore del sito.</p>