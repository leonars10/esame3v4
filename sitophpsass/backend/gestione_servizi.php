<?php
session_start();
// Assicurati che il percorso a db.php sia corretto e che usi PDO
require 'includes/db_backend.php';
require 'includes/utility_php.php'; // Contiene requireLoggedIn() e isValidCellContent()

// ESECUZIONE DEL CONTROLLO DI ACCESSO
requireLoggedIn();

$message = ""; // Messaggio di successo o errore
$edit_servizio = null; // Per memorizzare i dati dell'utente da modificare

// --- Gestione delle operazioni CRUD ---
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action === 'create_servizio') {
            $title = trim($_POST['title']);
            $description = $_POST['description'];
            
            // --- INIZIO: Verifica e Validazione LATO SERVER ---
            // Verifica Titolo (3-100) e Descrizione (10-500)
            if (!isValidCellContent($title, 3, 100) || !isValidCellContent($description, 10, 500)) {
                $message = "<span style='color:red;'>Errore: TITOLO (3-100 car.) e DESCRIZIONE (10-500 car.) non possono essere vuoti o non rispettare i limiti di lunghezza.</span>";
            } else {
                // Esecuzione INSERT
                try {
                    $stmt = $pdo->prepare("INSERT INTO servizi (title, description) VALUES (?, ?)");
                    $stmt->execute([$title, $description]);
                    $message = "<span style='color:green;'>TITLE '" . htmlspecialchars($title) . "' creato con successo!</span>";
                } catch (PDOException $e) {
                    if ($e->getCode() == 23000) { // Codice errore per duplicato (UNIQUE constraint)
                        $message = "<span style='color:red;'>Errore: il TITOLO '" . htmlspecialchars($title) . "' esiste già.</span>";
                    } else {
                        $message = "<span style='color:red;'>Errore durante la creazione del servizio: " . $e->getMessage() . "</span>";
                    }
                }
            }
        } elseif ($action === 'update_servizio') {
            $id = (int)$_POST['id'];
            $title = trim($_POST['title']);
            $description = $_POST['description']; 
            
            // Validazione lato server per l'aggiornamento
            if (!isValidCellContent($title, 3, 100)) {
                $message = "<span style='color:red;'>Errore: TITOLO (3-100 car.) non può essere vuoto o non rispettare i limiti di lunghezza.</span>";
            } 
            // Valida la descrizione SOLO SE è stata fornita (non vuota)
            elseif (!empty($description) && !isValidCellContent($description, 10, 500)) {
                 $message = "<span style='color:red;'>Errore: La DESCRIZIONE (se modificata) deve avere tra 10 e 500 caratteri.</span>";
            }
            else {
                try {
                    // Esecuzione UPDATE (titolo e descrizione, anche se description è vuota)
                    $stmt = $pdo->prepare("UPDATE servizi SET title = ?, description = ? WHERE id = ?");
                    $stmt->execute([$title, $description, $id]);
                    
                    $message = "<span style='color:green;'>TITOLO '" . htmlspecialchars($title) . "' aggiornato con successo!</span>";
                } catch (PDOException $e) {
                    if ($e->getCode() == 23000) {
                        $message = "<span style='color:red;'>Errore: IL TITOLO '" . htmlspecialchars($title) . "' esiste già.</span>";
                    } else {
                        $message = "<span style='color:red;'>Errore durante l'aggiornamento del TITOLO: " . $e->getMessage() . "</span>";
                    }
                }
            }
        } elseif ($action === 'delete_servizio') {
            $id = (int)$_POST['id'];
            try {
                $stmt = $pdo->prepare("DELETE FROM servizi WHERE id = ?");
                $stmt->execute([$id]);
                $message = "<span style='color:green;'>Servizio eliminato con successo!</span>";
            } catch (PDOException $e) {
                $message = "<span style='color:red;'>Errore durante l'eliminazione dell'utente: " . $e->getMessage() . "</span>";
            }
        }
    }
}

// --- Gestione della richiesta di modifica (GET) ---
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT id, title FROM servizi WHERE id = ?");
    $stmt->execute([$id]);
    $edit_servizio = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$edit_servizio) {
        $message = "<span style='color:red;'>Errore: Servizio non trovato per la modifica.</span>";
    }
}

// Recupera tutti i SERVIZI per la visualizzazione
$stmt = $pdo->query("SELECT id, title, description FROM servizi ORDER BY id ASC");
$titles = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione SERVIZI - Area Riservata</title>
    <link rel="stylesheet" href="css/gestione_unificata.min.css">
   
        <!-- Inclusione dello script JavaScript per la validazione lato CLIENT -->
    <script src="public/js/utility_java.js"></script>

        <script>

        // --- Funzione Principale di Validazione del Form (LATO CLIENT) ---
        function validateServiceForm() {
            const title = document.getElementById('title').value;
            const description = document.getElementById('description').value;
            const action = document.getElementById('form_action').value;
            
            let isValid = true;
            let errorMessage = "Errore di Validazione:\n\n";

            // Verifica Titolo (limiti: 3-100)
            if (!isValidCellContentClient(title, 3, 100)) {
                errorMessage += "• Il Nome Servizio deve avere tra 3 e 100 caratteri.\n";
                isValid = false;
            }

            // Verifica Descrizione (limiti: 10-500)
            if (action === 'create_servizio') {
                // Descrizione obbligatoria in creazione
                if (!isValidCellContentClient(description, 10, 500)) {
                    errorMessage += "• La Descrizione è obbligatoria e deve avere tra 10 e 500 caratteri.\n";
                    isValid = false;
                }
            } else if (action === 'update_servizio') {
                // Se è in update, e la descrizione NON è vuota, la validiamo
                if (description.trim().length > 0 && !isValidCellContentClient(description, 10, 500)) {
                    errorMessage += "• Se modificata, la Descrizione deve avere tra 10 e 500 caratteri.\n";
                    isValid = false;
                }
            }

            if (!isValid) {
                alert(errorMessage);
                return false; // Blocca l'invio del form
            }
            return true; // Consente l'invio del form
        }

        // Funzione per chiedere conferma prima dell'eliminazione del servizio
        function confirmDelete(title) {
            return confirm("Sei sicuro di voler eliminare il SERVIZIO '" + title + "'?");
        }

        // Funzione per popolare il form di modifica
        function populateEditForm(id, title, description) {
            document.getElementById('id').value = id;
            document.getElementById('title').value = title;
            document.getElementById('description').value = description; // Popola la descrizione
            document.getElementById('form_action').value = 'update_servizio';
            document.getElementById('form_submit_button').textContent = 'Aggiorna Servizio';
            document.getElementById('form_title').textContent = 'Modifica Servizio Esistente';
            document.getElementById('password_note').style.display = 'block'; // Mostra la nota
        }

        // Funzione per resettare il form
        function resetForm() {
            document.getElementById('id').value = '';
            document.getElementById('title').value = '';
            document.getElementById('description').value = '';
            document.getElementById('form_action').value = 'create_servizio';
            document.getElementById('form_submit_button').textContent = 'Crea Servizio';
            document.getElementById('form_title').textContent = 'Crea Nuovo Servizio';
            document.getElementById('password_note').style.display = 'none'; // Nasconde la nota
        }
    </script>
</head>
<body onload="resetForm()"> <header>
        <h1>Gestione Servizi</h1>
        <p>Area Riservata per la gestione dei SERVIZI al Backend.</p>
        <a href="index_FE.php" class="back-link">Torna alla Home</a>
        <a href="logout.php" class="logout-button">Logout</a>
    </header>

    <div class="container">
        <?php if (!empty($message)): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>

        <h2 id="form_title">Crea Nuovo Servizio</h2>
        <div class="user-form">
            <form method="POST" action="gestione_servizi.php" onsubmit="return validateServiceForm()"> <input type="hidden" name="action" id="form_action" value="create_servizio">
                <input type="hidden" name="id" id="id" value="<?php echo htmlspecialchars($edit_servizio['id'] ?? ''); ?>">

                <div class="form-group">
                    <label for="title">Nome Servizio:</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($edit_servizio['title'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="description">Descrizione (10-500 caratteri):</label>
                    <textarea id="description" name="description" rows="4"></textarea>
                  <!--  <small id="password_note" style="display:none; color:#6c757d;">Lascia vuoto per non modificare la descrizione in fase di aggiornamento.</small>. -->
                </div>
                <button type="submit" id="form_submit_button">Crea Servizio</button>
                <button type="reset" onclick="resetForm()">Annulla/Nuovo</button>
            </form>
        </div>

        <h2>Elenco SERVIZI</h2>
        <?php if (count($titles) > 0): ?>
            <table class="user-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Servizio</th>
                        <th>Descrizione</th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($titles as $title): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($title['id']); ?></td>
                            <td><?php echo htmlspecialchars($title['title']); ?></td>
                            <td><?php echo htmlspecialchars($title['description']); ?></td>
                            <td class="action-buttons">
                                <a href="javascript:void(0);" onclick="populateEditForm(<?php echo $title['id']; ?>, '<?php echo htmlspecialchars(addslashes($title['title'])); ?>', '<?php echo htmlspecialchars(addslashes($title['description'])); ?>')" class="edit-btn">Modifica</a>
                                <form method="POST" style="display:inline;" onsubmit="return confirmDelete('<?php echo htmlspecialchars(addslashes($title['title'])); ?>');">
                                    <input type="hidden" name="action" value="delete_servizio">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($title['id']); ?>">
                                    <button type="submit" class="delete-btn">Cancella</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="text-align: center;">Nessun servizio registrato.</p>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; 2025 Il Tuo Sito. Tutti i diritti riservati.</p>
    </footer>
</body>
</html>