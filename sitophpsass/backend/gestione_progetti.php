<?php
// debug: mostra gli errori temporaneamente (rimuovi/commenta in produzione)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// avvia la sessione PHP per gestire lo stato dell'utente
session_start();

// Configurazioni upload (usate anche da utility_php)
const UPLOAD_DIR_FS = __DIR__ . '/../Img/';
const UPLOAD_DIR_WEB = 'Img/';
const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif'];
const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5 MB

// Includi il file di connessione al database e le funzioni di utilità
require 'includes/db_backend.php';
require_once 'includes/utility_php.php';

// Controllo accesso
requireLoggedIn();

// Variabili di stato
$message = "";
$edit_progetto = null;
$edit_immagine = null;

// POST CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    // PROGETTI: create / update / delete
    if ($action === 'create_progetto' || $action === 'update_progetto') {
        $id_progetto = isset($_POST['id_progetto']) && $_POST['id_progetto'] !== '' ? (int)$_POST['id_progetto'] : null;

        // ora leggiamo i nuovi name dagli input:
        $alt_immagine_principale = sanitizeInput($_POST['alt_immagine_principale'] ?? '');
        $descrizione = sanitizeInput($_POST['descrizione'] ?? '');
        $tipo = sanitizeInput($_POST['tipo'] ?? '');
        $nome_progetto = sanitizeInput($_POST['nome_progetto'] ?? '');

        // recupera eventuale immagine vecchia se update
        $old_img = null;
        if ($action === 'update_progetto' && $id_progetto) {
            try {
                $stmt_old = $pdo->prepare("SELECT src_immagine_principale FROM progetti WHERE id_progetto = ?");
                $stmt_old->execute([$id_progetto]);
                $old_img = $stmt_old->fetchColumn();
            } catch (Exception $e) {
                $old_img = null;
            }
        }

        $errors = [];
        // Validazione lato server usando gli stessi limiti
        if (!isValidCellContent($alt_immagine_principale, 3, 100)) $errors[] = "ALT Immagine non valido (3-100 caratteri).";
        if (!isValidCellContent($descrizione, 10, 500)) $errors[] = "Descrizione non valida (10-500 caratteri).";
        if (!isValidCellContent($tipo, 2, 50)) $errors[] = "Tipo non valido (2-50 caratteri).";
        if (!isValidCellContent($nome_progetto, 3, 100)) $errors[] = "Nome progetto non valido (3-100 caratteri).";

        if ($errors) {
            $message = "<span style='color:red;'>Errore di validazione: " . implode(' | ', $errors) . "</span>";
        } else {
            // gestisci upload immagine principale (se presente). Alla creazione è richiesto.
            $upload = handleImageUpload('immagine_principale_file', $old_img);
            if (!$upload['success']) {
                // handleImageUpload ha già impostato $message
                goto end_image_action;
            }
            // il path dell'immagine principale in DB è src_immagine_principale
            $src_immagine_principale = $upload['path'] ?? $old_img;

            if ($action === 'create_progetto' && (empty($src_immagine_principale))) {
                $message = "<span style='color:red;'>Errore: immagine principale richiesta per la creazione del progetto.</span>";
                goto end_image_action;
            }

            try {
                if ($action === 'create_progetto') {
                    // verifica se id_progetto è AUTO_INCREMENT
                    $colInfo = $pdo->query("SHOW COLUMNS FROM progetti LIKE 'id_progetto'")->fetch(PDO::FETCH_ASSOC);
                    $isAuto = $colInfo && isset($colInfo['Extra']) && stripos($colInfo['Extra'], 'auto_increment') !== false;

                    if ($isAuto) {
                        $stmt = $pdo->prepare("INSERT INTO progetti (alt_immagine_principale, descrizione, tipo, src_immagine_principale, nome_progetto, link_dettaglio) VALUES (?, ?, ?, ?, ?, ?)");
                        $stmt->execute([$alt_immagine_principale, $descrizione, $tipo, $src_immagine_principale, $nome_progetto, '']);
                        $id_progetto = (int)$pdo->lastInsertId();
                    } else {
                        $next = $pdo->query("SELECT COALESCE(MAX(id_progetto),0)+1 AS next_id FROM progetti")->fetch(PDO::FETCH_ASSOC);
                        $id_progetto = $next ? (int)$next['next_id'] : 1;
                        $stmt = $pdo->prepare("INSERT INTO progetti (id_progetto, alt_immagine_principale, descrizione, tipo, src_immagine_principale, nome_progetto, link_dettaglio) VALUES (?, ?, ?, ?, ?, ?, ?)");
                        $stmt->execute([$id_progetto, $alt_immagine_principale, $descrizione, $tipo, $src_immagine_principale, $nome_progetto, '']);
                    }

                    // genera link_dettaglio e aggiorna
                    $link_dettaglio = 'dettagliolavoro.php?project=' . $id_progetto;
                    $stmt_up = $pdo->prepare("UPDATE progetti SET link_dettaglio = ? WHERE id_progetto = ?");
                    $stmt_up->execute([$link_dettaglio, $id_progetto]);

                    $message = "<span style='color:green;'>Progetto '" . htmlspecialchars($nome_progetto, ENT_QUOTES) . "' creato con successo (ID: " . $id_progetto . ").</span>";
                } else {
                    if (empty($id_progetto)) throw new Exception("ID progetto mancante per l'aggiornamento.");
                    $link_dettaglio = 'dettagliolavoro.php?project=' . $id_progetto;
                    $stmt = $pdo->prepare("UPDATE progetti SET alt_immagine_principale=?, descrizione=?, tipo=?, src_immagine_principale=?, nome_progetto=?, link_dettaglio=? WHERE id_progetto=?");
                    $stmt->execute([$alt_immagine_principale, $descrizione, $tipo, $src_immagine_principale, $nome_progetto, $link_dettaglio, $id_progetto]);
                    $message = "<span style='color:green;'>Progetto '" . htmlspecialchars($nome_progetto, ENT_QUOTES) . "' aggiornato con successo!</span>";
                }
            } catch (PDOException $e) {
                $message = "<span style='color:red;'>Errore DB progetto: " . htmlspecialchars($e->getMessage(), ENT_QUOTES) . " (SQLSTATE " . $e->getCode() . ")</span>";
            } catch (Exception $e) {
                $message = "<span style='color:red;'>Errore: " . htmlspecialchars($e->getMessage(), ENT_QUOTES) . "</span>";
            }
        }
    } elseif ($action === 'delete_progetto') {
        $id = (int)($_POST['id_progetto'] ?? 0);
        try {
            $stmt_get_images = $pdo->prepare("SELECT src_immagine FROM immagini_lavoro WHERE id_progetto = ?");
            $stmt_get_images->execute([$id]);
            $images = $stmt_get_images->fetchAll(PDO::FETCH_COLUMN);
            foreach ($images as $img) {
                $fs = UPLOAD_DIR_FS . basename($img);
                if (file_exists($fs) && is_file($fs)) @unlink($fs);
            }
            // elimina anche immagine principale del progetto
            $stmt_get_proj_img = $pdo->prepare("SELECT src_immagine_principale FROM progetti WHERE id_progetto = ?");
            $stmt_get_proj_img->execute([$id]);
            $proj_img = $stmt_get_proj_img->fetchColumn();
            if ($proj_img) {
                $fs = UPLOAD_DIR_FS . basename($proj_img);
                if (file_exists($fs) && is_file($fs)) @unlink($fs);
            }

            $stmt = $pdo->prepare("DELETE FROM progetti WHERE id_progetto = ?");
            $stmt->execute([$id]);
            $message = "<span style='color:green;'>Progetto eliminato con successo.</span>";
        } catch (PDOException $e) {
            $message = "<span style='color:red;'>Errore durante l'eliminazione del progetto: " . htmlspecialchars($e->getMessage(), ENT_QUOTES) . "</span>";
        }
    }

    // iMMAGINI_LAVORO: create / update / delete (nessuna modifica qui sui nomi POST)
    if ($action === 'create_immagine' || $action === 'update_immagine') {
        $id_lavoro = isset($_POST['id_lavoro']) && $_POST['id_lavoro'] !== '' ? (int)$_POST['id_lavoro'] : null;
        $id_progetto_img = isset($_POST['id_progetto_img']) ? (int)$_POST['id_progetto_img'] : 0;
        $alt_immagine = sanitizeInput($_POST['alt_immagine'] ?? '');

        $errors = [];
        if ($id_progetto_img <= 0) $errors[] = "Devi selezionare un progetto valido.";
        if (!isValidCellContent($alt_immagine, 5, 255)) $errors[] = "Alt immagine non valido (5-255 caratteri).";

        $old_src = '';
        if ($action === 'update_immagine' && $id_lavoro) {
            $stmt_old = $pdo->prepare("SELECT src_immagine FROM immagini_lavoro WHERE id_lavoro = ?");
            $stmt_old->execute([$id_lavoro]);
            $old_src = $stmt_old->fetchColumn();
        }

        if ($errors) {
            $message = "<span style='color:red;'>Errore di validazione (Immagine): " . implode(' | ', $errors) . "</span>";
            goto end_image_action;
        }

        $upload = handleImageUpload('src_immagine', $old_src);
        if (!$upload['success']) goto end_image_action;
        $src_path = $upload['path'];

        try {
            if ($action === 'create_immagine') {
                if (empty($src_path)) {
                    $message = "<span style='color:red;'>Errore: immagine richiesta per la creazione.</span>";
                    goto end_image_action;
                }
                $stmt = $pdo->prepare("INSERT INTO immagini_lavoro (id_progetto, src_immagine, alt_immagine) VALUES (?, ?, ?)");
                $stmt->execute([$id_progetto_img, $src_path, $alt_immagine]);
                $new_id = (int)$pdo->lastInsertId();
                $message = "<span style='color:green;'>Immagine creata con successo (ID: {$new_id}).</span>";
            } else {
                if (empty($id_lavoro)) throw new Exception("ID lavoro mancante per l'aggiornamento.");
                $stmt = $pdo->prepare("UPDATE immagini_lavoro SET id_progetto=?, src_immagine=?, alt_immagine=? WHERE id_lavoro=?");
                $stmt->execute([$id_progetto_img, $src_path, $alt_immagine, $id_lavoro]);
                $message = "<span style='color:green;'>Immagine aggiornata con successo.</span>";
            }
        } catch (PDOException $e) {
            $message = "<span style='color:red;'>Errore DB immagine: " . htmlspecialchars($e->getMessage(), ENT_QUOTES) . "</span>";
            if (!empty($src_path) && $src_path !== $old_src) {
                $just_fs = UPLOAD_DIR_FS . basename($src_path);
                if (file_exists($just_fs)) @unlink($just_fs);
            }
        } catch (Exception $e) {
            $message = "<span style='color:red;'>Errore: " . htmlspecialchars($e->getMessage(), ENT_QUOTES) . "</span>";
        }
    } elseif ($action === 'delete_immagine') {
        $id = (int)($_POST['id_lavoro'] ?? 0);
        try {
            $stmt_get = $pdo->prepare("SELECT src_immagine FROM immagini_lavoro WHERE id_lavoro = ?");
            $stmt_get->execute([$id]);
            $file = $stmt_get->fetchColumn();
            $stmt = $pdo->prepare("DELETE FROM immagini_lavoro WHERE id_lavoro = ?");
            $stmt->execute([$id]);
            if ($file) {
                $fs = UPLOAD_DIR_FS . basename($file);
                if (file_exists($fs) && is_file($fs)) @unlink($fs);
            }
            $message = "<span style='color:green;'>Immagine eliminata con successo.</span>";
        } catch (PDOException $e) {
            $message = "<span style='color:red;'>Errore durante l'eliminazione dell'immagine: " . htmlspecialchars($e->getMessage(), ENT_QUOTES) . "</span>";
        }
    }

    end_image_action: ;
}

// Recupera dati per vista / edit
if (isset($_GET['edit_progetto_id'])) {
    $id = (int)$_GET['edit_progetto_id'];
    $stmt = $pdo->prepare("SELECT * FROM progetti WHERE id_progetto = ?");
    $stmt->execute([$id]);
    $edit_progetto = $stmt->fetch(PDO::FETCH_ASSOC);
}
if (isset($_GET['edit_immagine_id'])) {
    $id = (int)$_GET['edit_immagine_id'];
    $stmt = $pdo->prepare("SELECT * FROM immagini_lavoro WHERE id_lavoro = ?");
    $stmt->execute([$id]);
    $edit_immagine = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Lista progetti e immagini per tabella/select
$stmt_progetti = $pdo->query("SELECT * FROM progetti ORDER BY id_progetto ASC");
$progetti = $stmt_progetti->fetchAll(PDO::FETCH_ASSOC);

$stmt_immagini = $pdo->query("SELECT il.*, p.nome_progetto FROM immagini_lavoro il JOIN progetti p ON il.id_progetto = p.id_progetto ORDER BY il.id_progetto, il.id_lavoro ASC");
$immagini = $stmt_immagini->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Gestione Progetti - Area Riservata</title>
    <link rel="stylesheet" href="css/gestione_unificata.min.css">

    <!-- utility JS condiviso -->
    <script src="public/js/utility_java.js"></script>
    <!-- variabile lista estensioni per lo script pagina -->
    <script>window.ALLOWED_EXTENSIONS_CLIENT = <?php echo json_encode(ALLOWED_EXTENSIONS); ?>;</script>
    <!-- script specifico della pagina (sposta qui il JS che era inline) -->
    <script src="public/js/gestione_progetti.js"></script>

    <!-- passaggio dati edit al JS esterno -->
    <script>
        window.__EDIT_PROGETTO = <?php echo json_encode($edit_progetto ?? null, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT); ?>;
        window.__EDIT_IMMAGINE = <?php echo json_encode($edit_immagine ?? null, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT); ?>;
    </script>
</head>
<body>
<header>
    <h1>Gestione Progetti e Lavori</h1>
    <p>Area Riservata per la gestione del portfolio.</p>
    <a href="index_FE.php" class="back-link">Torna alla Home</a>
    <a href="logout.php" class="logout-button">Logout</a>
</header>

<div class="container">
    <?php if (!empty($message)): ?>
        <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>

    <h2 id="progetto_form_title">Crea / Modifica Progetto</h2>
    <div class="data-form">
        <form id="progetto_form" method="POST" action="gestione_progetti.php" enctype="multipart/form-data" onsubmit="return validateProgettoForm()">
            <input type="hidden" name="action" id="progetto_form_action" value="create_progetto">
            <input type="hidden" id="progetto_id" name="id_progetto" value="<?php echo htmlspecialchars($edit_progetto['id_progetto'] ?? ''); ?>">
            <input type="hidden" id="progetto_old_immagine_principale" name="old_immagine_principale" value="<?php echo htmlspecialchars($edit_progetto['src_immagine_principale'] ?? ''); ?>">

            <!-- Nome Progetto prima -->
            <div class="form-group"><label>Nome Progetto</label><input type="text" id="progetto_nome_progetto" name="nome_progetto" value="<?php echo htmlspecialchars($edit_progetto['nome_progetto'] ?? ''); ?>" required></div>

            <div class="form-group"><label>Descrizione</label><textarea id="progetto_descrizione" name="descrizione" required><?php echo htmlspecialchars($edit_progetto['descrizione'] ?? ''); ?></textarea></div>
            <div class="form-group"><label>Tipo</label><input type="text" id="progetto_tipo" name="tipo" value="<?php echo htmlspecialchars($edit_progetto['tipo'] ?? ''); ?>" required></div>

            <div class="form-group">
                <label>File Immagine Principale</label>
                <input type="file" id="progetto_src_immagine" name="immagine_principale_file" accept="<?php echo '.' . implode(',.', ALLOWED_EXTENSIONS); ?>" <?php echo $edit_progetto ? '' : 'required'; ?>>
                <small id="progetto_file_input_note" style="display:none;color:#b00;">Lascia vuoto per mantenere file attuale.</small>
            </div>
            <div class="image-preview-container">
                <p id="progetto_current_src_path" style="display:none;color:#666;font-size:0.9em;"></p>
                <img id="progetto_image_preview" src="<?php echo htmlspecialchars($edit_progetto['src_immagine_principale'] ?? '', ENT_QUOTES); ?>" style="display:<?php echo $edit_progetto ? 'block' : 'none'; ?>;max-width:150px;">
                <p id="progetto_preview_filename"><?php echo $edit_progetto ? htmlspecialchars(basename($edit_progetto['src_immagine_principale'] ?? '')) : 'Nessuna immagine selezionata.'; ?></p>
            </div>

            <div class="form-group"><label>ALT Immagine</label><input type="text" id="progetto_alt_immagine_principale" name="alt_immagine_principale" value="<?php echo htmlspecialchars($edit_progetto['alt_immagine_principale'] ?? ''); ?>" required></div>

            <div class="form-actions">
                <button type="submit" id="progetto_submit_button">Crea Progetto</button>
                <button type="button" onclick="resetProgettoForm()">Annulla</button>
            </div>
        </form>
    </div>

    <section>
        <h2>Progetti Esistenti</h2>
        <?php if (empty($progetti)): ?>
            <p>Nessun progetto.</p>
        <?php else: ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID Progetto</th>
                        <th>Nome Progetto</th>
                        <th>Descrizione</th>
                        <th>Tipo</th>
                        <th>Immagine</th>
                        <th>ALT Immagine</th>
                        <th>Preview</th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($progetti as $progetto):
                    $proj_img_src = $progetto['src_immagine_principale'] ?? '';
                    if ($proj_img_src && strpos($proj_img_src, '../') !== 0) $proj_img_src = '../' . ltrim($proj_img_src, '/');
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($progetto['id_progetto']); ?></td>
                        <td class="name-col"><?php echo htmlspecialchars($progetto['nome_progetto']); ?></td>
                        <td class="description"><?php echo htmlspecialchars($progetto['descrizione']); ?></td>
                        <td><?php echo htmlspecialchars($progetto['tipo']); ?></td>
                        <td class="image-path"><?php echo htmlspecialchars(basename($progetto['src_immagine_principale'] ?? '')); ?></td>
                        <td><?php echo htmlspecialchars($progetto['alt_immagine_principale'] ?? ''); ?></td>
                        <td class="preview"><?php if (!empty($proj_img_src)): ?><img src="<?php echo htmlspecialchars($proj_img_src, ENT_QUOTES); ?>" alt="<?php echo htmlspecialchars($progetto['nome_progetto'] ?? '', ENT_QUOTES); ?>" style="max-width:100px;"><?php endif; ?></td>
                        <td class="action-buttons">
                            <a href="javascript:void(0);" onclick='populateProgettoForm(<?php echo (int)$progetto['id_progetto']; ?>, <?php echo json_encode($progetto['alt_immagine_principale'] ?? '', JSON_HEX_APOS|JSON_HEX_QUOT); ?>, <?php echo json_encode($progetto['descrizione'] ?? '', JSON_HEX_APOS|JSON_HEX_QUOT); ?>, <?php echo json_encode($progetto['tipo'] ?? '', JSON_HEX_APOS|JSON_HEX_QUOT); ?>, <?php echo json_encode($progetto['src_immagine_principale'] ?? '', JSON_HEX_APOS|JSON_HEX_QUOT); ?>, <?php echo json_encode($progetto['nome_progetto'] ?? '', JSON_HEX_APOS|JSON_HEX_QUOT); ?>)' class="edit-btn">Modifica</a>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Confermi eliminazione del progetto?');">
                                <input type="hidden" name="action" value="delete_progetto">
                                <input type="hidden" name="id_progetto" value="<?php echo htmlspecialchars($progetto['id_progetto']); ?>">
                                <button type="submit" class="delete-btn">Cancella</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>

    <hr>

    <section>
        <h2 id="immagine_form_title">Crea / Modifica Immagine Lavoro</h2>
        <div class="data-form">
            <form id="immagine_form" method="POST" action="gestione_progetti.php" enctype="multipart/form-data" onsubmit="return validateImmagineForm()">
                <input type="hidden" name="action" id="immagine_form_action" value="create_immagine">
                <input type="hidden" id="immagine_id_lavoro" name="id_lavoro" value="<?php echo htmlspecialchars($edit_immagine['id_lavoro'] ?? ''); ?>">
                <div class="form-group">
                    <label for="immagine_id_progetto">Progetto</label>
                    <select id="immagine_id_progetto" name="id_progetto_img" required>
                        <option value="">-- Seleziona --</option>
                        <?php foreach ($progetti as $p): $sel = (isset($edit_immagine['id_progetto']) && $edit_immagine['id_progetto'] == $p['id_progetto']) ? 'selected' : ''; ?>
                            <option value="<?php echo (int)$p['id_progetto']; ?>" <?php echo $sel; ?>><?php echo htmlspecialchars($p['id_progetto'] . ' - ' . $p['nome_progetto']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>File Immagine</label>
                    <input type="file" id="immagine_src_immagine" name="src_immagine" accept="<?php echo '.' . implode(',.', ALLOWED_EXTENSIONS); ?>" <?php echo $edit_immagine ? '' : 'required'; ?>>
                    <small id="file_input_note" style="display:none;color:#b00;">Lascia vuoto per mantenere file attuale.</small>
                </div>
                <div class="image-preview-container">
                    <p id="current_src_path" style="display:none;color:#666;font-size:0.9em;"></p>
                    <img id="image_preview" src="<?php echo htmlspecialchars($edit_immagine['src_immagine'] ?? '', ENT_QUOTES); ?>" style="display:<?php echo $edit_immagine ? 'block' : 'none'; ?>;max-width:150px;">
                    <p id="preview_filename"><?php echo $edit_immagine ? htmlspecialchars(basename($edit_immagine['src_immagine'])) : 'Nessuna immagine selezionata.'; ?></p>
                </div>
                <div class="form-group">
                    <label>ALT Immagine</label>
                    <input type="text" id="immagine_alt_immagine" name="alt_immagine" value="<?php echo htmlspecialchars($edit_immagine['alt_immagine'] ?? ''); ?>" required>
                </div>
                <div class="form-actions">
                    <button type="submit" id="immagine_submit_button">Crea Immagine</button>
                    <button type="button" onclick="resetImmagineForm()">Annulla</button>
                </div>
            </form>
        </div>
    </section>

    <section>
        <h2>Immagini Lavoro</h2>
        <?php if (empty($immagini)): ?>
            <p>Nessuna immagine lavoro.</p>
        <?php else: ?>
            <table class="data-table">
               <thead>
                    <tr>
                        <th>ID Lavoro</th>
                        <th>ID Progetto</th>
                        <th>Nome Progetto</th>
                        <th>Immagini</th>
                        <th>ALT Immagine</th>
                        <th>Preview</th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($immagini as $img):
                    $img_src = $img['src_immagine'];
                    if ($img_src && strpos($img_src, '../') !== 0) $img_src = '../' . ltrim($img_src, '/');
                ?>
                    <tr>
                        <td><?php echo (int)$img['id_lavoro']; ?></td>
                        <td><?php echo (int)$img['id_progetto']; ?></td>
                        <td><?php echo htmlspecialchars($img['nome_progetto']); ?></td>
                        <td><?php echo htmlspecialchars(basename($img['src_immagine'])); ?></td>
                        <td><?php echo htmlspecialchars($img['alt_immagine']); ?></td>
                        <td><img src="<?php echo htmlspecialchars($img_src, ENT_QUOTES); ?>" alt="<?php echo htmlspecialchars($img['alt_immagine'], ENT_QUOTES); ?>" style="max-width:100px;"></td>
                        <td class="action-buttons">
                         <button class="edit-btn" onclick='populateImmagineForm(<?php echo (int)$img['id_lavoro']; ?>, <?php echo (int)$img['id_progetto']; ?>, <?php echo json_encode($img['src_immagine'], JSON_HEX_APOS|JSON_HEX_QUOT); ?>, <?php echo json_encode($img['alt_immagine'], JSON_HEX_APOS|JSON_HEX_QUOT); ?>)'>Modifica</button>
                            <form method="POST" action="gestione_progetti.php" style="display:inline;" onsubmit="return confirm('Confermi eliminazione immagine?');">
                                <input type="hidden" name="action" value="delete_immagine">
                                <input type="hidden" name="id_lavoro" value="<?php echo (int)$img['id_lavoro']; ?>">
                            <button type="submit" class="delete-btn">Cancella</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>
</div>

<footer>&copy;2025 Leonardo Cipollini - Tutti i diritti riservati</footer>
</body>
</html>