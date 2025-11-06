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
    <?php includeCSS($lavori_css); ?> 
</head>

<body>

<?php renderHeader($site_name, $menu_items); ?>



<?php
// Connessione al database MySQL
require_once 'includes/db.php';

// Query per ottenere i lavori_video
try {
    $stmt = $pdo->query("SELECT * from lavori_video");
    $json_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('<p style="color:red; text-align:center;">Errore nella lettura dei servizi dal database.</p>');
}



$json_data_video = $json_data; 

if ($json_data_video === null) {
    die('Error file ');
}

?>

<!-- Sfondo -->
<section class="sfondo">
    <div class="sfondo-content">
        <h2>Lavori eseguiti</h2>
    </div>
</section>

<section class="video-section">
    <div class="video-container">
        <video controls autoplay>  <?php foreach ($json_data_video as $item) { if ($item["id"] == 1 ) { echo "<source src='{$item['src']}' titolo='{$item['titolo']}' type='{$item['type']}'>"; }} ?>
        </video>
    </div>
</section>

<!-- Progetti -->

<div class="projects-container">
<?php
// Connessione al database MySQL
require_once 'includes/db.php';

// Query per ottenere i lavori dispari
// Questa query seleziona i lavori con ID dispari
// e recupera i campi 'link_dettaglio', 'image_principale', 'titolo' e 'descrizione
try {
   $stmt = $pdo->query("SELECT link_dettaglio, src_immagine_principale, alt_immagine_principale, descrizione FROM progetti");
    $json_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('<p style="color:red; text-align:center;">Errore nella lettura dei servizi dal database.</p>');
}


$oddIds=$json_data;

?>



<?php foreach ($oddIds as $project): ?>
        <div class="project-item">
            <a href="<?= htmlspecialchars($project['link_dettaglio']); ?>" title="<?= htmlspecialchars($project['alt_immagine_principale']); ?>">
                <div class="project-image">
                    <img 
                        src="<?= htmlspecialchars($project['src_immagine_principale']); ?>" 
                        alt="<?= htmlspecialchars($project['alt_immagine_principale']); ?>" 
                        loading="lazy">
                </div>
                <div class="project-name"><?= htmlspecialchars($project['alt_immagine_principale']); ?></div>
            </a>
        </div>
    <?php endforeach; ?>
</div>  


<?php
renderFooter($current_year, $site_name);
?>

</body> 
</html>
