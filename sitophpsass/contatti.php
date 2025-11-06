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
    <?php includeCSS($contatti_css); ?> 
</head>

<body>

<?php renderHeader($site_name, $menu_items); ?>


<?php

// Connessione al database MySQL
require_once 'includes/db.php';

// Query per ottenere i dati di "Chi sono"
try {
    $stmt = $pdo->query("SELECT * FROM chi_sono");
    $json_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('<p style="color:red; text-align:center;">Errore nella lettura dei servizi dal database.</p>');
}



// Check if the JSON was decoded successfully
if ($json_data === null) {
    die('Error decoding the JSON file');
}

$righeDesiderate = array_filter($json_data, function($riga) {
    return $riga['id'] == 1 ; 
 });


?>


<!-- Sfondo -->
<section class="sfondo">
    <div class="sfondo-content">
        <?php foreach ($json_data as $item) { if ($item["id"] == 1 ) { echo "<h2>{$item['title']}</h2>"; } }  ?>
    </div>
</section>



<!-- Servizi -->

<section class="services">
    <div class="container">
       <?php foreach ($json_data as $item) { if ($item["id"] == 1 ) { echo "<h2>{$item['articolo']}</h2>"; } }  ?>
            <div class="service-item">
                <?php foreach ($json_data as $item) { if ($item["id"] == 1 ) { echo "<p>{$item['description']}</p>"; } }  ?>
            </div>
    </div>
</section>



<?php include 'includes/contact_form.php'; ?>


<?php
renderFooter($current_year, $site_name);
?>
</body>
</html>