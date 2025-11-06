<?php
include 'includes/config.php';
include 'includes/header.php';
include 'includes/footer.php';

?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($site_name); ?></title>
    <?php includeCSS($servizi_css); ?> 
</head>

<body>

<?php renderHeader($site_name, $menu_items); ?>


<!-- Sfondo -->
<section class="sfondo">
    <div class="sfondo-content">
        <h2>Servizi</h2>
    </div>
</section>

<?php
// Connessione al database MySQL
require_once 'includes/db.php';

// Query per ottenere i servizi
try {
    $stmt = $pdo->query("SELECT title, description FROM servizi");
    $json_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('<p style="color:red; text-align:center;">Errore nella lettura dei servizi dal database.</p>');
}

//$conn->close();
$json = true; // Per mantenere la logica di controllo errori successiva

// Controllo errori nella lettura e decodifica JSON
if ($json === false) {
    die('<p style="color:red; text-align:center;">Impossibile leggere i dati dei servizi.</p>');
}
if ($json_data === null) {
    die('<p style="color:red; text-align:center;">I dati dei servizi non sono disponibili.</p>');
}
?>

<section class="services"> 
    <div class="container">
    <!--    <h2>I MIEI SERVIZI</h2>  -->
        <?php foreach ($json_data as $service): ?>
            <div class="service-item">
              <h3><?= htmlspecialchars($service['title']); ?></h3>
              <p><?= htmlspecialchars($service['description']); ?></p> 
            </div>
        <?php endforeach; ?>
    </div>
</section> 

<?php include 'includes/contact_form.php'; ?>

<?php renderFooter($current_year, $site_name); ?>

</body>
</html>
