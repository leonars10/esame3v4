<?php
include 'includes/config.php';
include 'includes/header.php';
include 'includes/footer.php';

// Determina il progetto da mostrare (default: 1 se non specificato)
$project = isset($_GET['project']) ? (int)$_GET['project'] : 1;
// RIMOSSA LA LIMITAZIONE: Non c'è più un 'if ($project < 1 || $project > X)'
// Questo significa che il sistema cercherà qualsiasi ID progetto passato.
// È fondamentale che l'ID passato esista nel tuo database per evitare errori.


// Connessione al database MySQL
require_once 'includes/db.php';

// Query per ottenere tutti i dettagli del progetto e le immagini associate
// per l'id_progetto specificato direttamente dal parametro 'project'.
try {
    $stmt = $pdo->prepare("SELECT
                               a.id_progetto,
                               a.alt_immagine_principale,
                               a.descrizione,
                               a.tipo,
                               a.src_immagine_principale AS immagine_principale_progetto,
                               a.nome_progetto,
                               a.link_dettaglio,
                               b.src_immagine,
                               b.alt_immagine,
                               b.id_lavoro
                           FROM progetti a
                           JOIN immagini_lavoro b ON a.id_progetto = b.id_progetto
                           WHERE a.id_progetto = ?
                           ORDER BY b.id_lavoro ASC");
    $stmt->execute([$project]);
    $json_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Se non ci sono risultati, il progetto potrebbe non esistere o non avere immagini associate.
    if (empty($json_data)) {
        $item = null;
        $gallery_items = [];
        $error = "Progetto non trovato o senza immagini associate.";
    } else {
        // Il primo elemento dei risultati contiene i dettagli del progetto
        $item = [
            'id_progetto' => $json_data[0]['id_progetto'],
            'titolo' => $json_data[0]['alt_immagine_principale'],
            'descrizione' => $json_data[0]['descrizione'],
            'tipo' => $json_data[0]['tipo'],
            'immagine_principale' => $json_data[0]['immagine_principale_progetto'],
            'nome_progetto' => $json_data[0]['nome_progetto'],
            'link_dettaglio' => $json_data[0]['link_dettaglio']
        ];

        // Tutte le righe di $json_data rappresentano le immagini della galleria per questo progetto
        $gallery_items = $json_data;
    }

} catch (PDOException $e) {
    die('<p style="color:red; text-align:center;">Errore nella lettura dei progetti dal database: ' . htmlspecialchars($e->getMessage()) . '</p>');
}

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($item ? $item['alt_immagine_principale'] . ' - ' . $site_name : $site_name); ?></title>
   <?php includeCSS($dettaglilavoro_css); ?>
</head>
<body>
    <?php renderHeader($site_name, $menu_items); ?>

    <section class="sfondo">
        <div class="sfondo-content">
            <h2><?= $item ? htmlspecialchars($item['titolo']) : 'Progetto non trovato'; ?></h2>
        </div>
    </section>

    <section class="services">
        <div class="container">
            <div class="service-item">
                <div class="service-title">
                    <h3><?= $item ? htmlspecialchars($item['tipo']) : 'N/A'; ?></h3>
                </div>
                <div class="service-description">
                    <p><?= $item ? htmlspecialchars($item['descrizione']) : 'Descrizione non disponibile'; ?></p>
                </div>
            </div>
        </div>
    </section>

    <div class="image-gallery">
        <?php if (empty($gallery_items)): ?>
            <p>Nessuna immagine disponibile per questo progetto.</p>
        <?php else: ?>
            <?php foreach ($gallery_items as $image): ?>
                <div class="gallery-item">
                    <img
                        src="<?= htmlspecialchars($image['src_immagine']); ?>"
                        title="<?= htmlspecialchars($image['alt_immagine']); ?>"
                        alt="<?= htmlspecialchars($image['alt_immagine']); ?>"
                        loading="lazy">
                    <div class="project-name"><?= htmlspecialchars($image['alt_immagine']); ?></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php if (isset($error)): ?>
        <p class="error"><?= $error; ?></p>
    <?php endif; ?>

    <?php renderFooter($current_year, $site_name); ?>
</body>
</html>