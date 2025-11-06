<?php
// Avvia la sessione PHP per gestire lo stato dell'utente
session_start();

// Includi il file di connessione al database e le funzioni di utilità
require 'includes/db_backend.php';
require_once 'includes/utility_php.php';

// ESECUZIONE DEL CONTROLLO DI ACCESSO
requireLoggedIn();

// Inizializza le variabili per i messaggi e per i dati di modifica
$message = ""; // Messaggio di successo o errore
$edit_user = null; // Per memorizzare i dati dell'utente da modificare

// --- Gestione delle operazioni CRUD (Create, Read, Update, Delete) ---
// Questo blocco gestisce le richieste POST per creare, aggiornare o eliminare un utente.
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        // Logica per la creazione di un nuovo utente
        if ($action === 'create_user') {
            $username = trim($_POST['username']);
            $password = $_POST['password'];

// --- INIZIO: Verifica e Validazione LATO SERVER ---
            // Controlla che i campi non siano vuoti e rispettino i requisiti minimi di lunghezza
            if (!isValidCellContent($username, 3, 50) || !isValidCellContent($password, 10, 255)) {
                $message = "<span style='color:red;'>Errore: Username (min 3) e password (min 10) non possono essere vuoti o troppo corti.</span>";
            } else {
         // usa la funzione esterna per la complessità della password
                $pwdResult = validatePasswordComplexityServer($password, 10, 3, 3, 2, 2, ALLOWED_SPECIALS);
                if ($pwdResult['ok'] === false) { // <--- CORREZIONE: Controlla la chiave 'ok'
                $message = "<span style='color:red;'>Errore password: " . htmlspecialchars($pwdResult['reason'], ENT_QUOTES) . "</span>"; // <--- CORREZIONE: Stampa la chiave 'reason'
                } else {
                // Esegue l'azione solo se i dati sono validi
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                try {
                    $stmt = $pdo->prepare("INSERT INTO utenti (username, password) VALUES (?, ?)");
                    $stmt->execute([$username, $hashed_password]);
                    $message = "<span style='color:green;'>Utente '" . htmlspecialchars($username) . "' creato con successo!</span>";
                } catch (PDOException $e) {
                    if ($e->getCode() == 23000) {
                        $message = "<span style='color:red;'>Errore: L'username '" . htmlspecialchars($username) . "' esiste già.</span>";
                    } else {
                        $message = "<span style='color:red;'>Errore durante la creazione dell'utente: " . $e->getMessage() . "</span>";
                    }
                }
            }
        }
// --- FINE: Verifica e Validazione LATO SERVER --- 
        // Logica per l'aggiornamento di un utente esistente
        } elseif ($action === 'update_user') {
            $id = (int)$_POST['user_id'];
            $username = trim($_POST['username']);
            $password = $_POST['password'];


            // Controlla la validità dei dati di aggiornamento
            if (!isValidCellContent($username, 3, 50)) {
                $message = "<span style='color:red;'>Errore: Username non può essere vuoto o troppo corto.</span>";
            } elseif (!empty($password) && !isValidCellContent($password, 10, 255)) {
                $message = "<span style='color:red;'>Errore: La nuova password deve avere almeno 10 caratteri.</span>";
            } else {
                 // Eseguire il controllo di complessità
                $pwdResult = validatePasswordComplexityServer($password, 10, 3, 3, 2, 2, ALLOWED_SPECIALS);
                if ($pwdResult['ok'] === false) { // Attenzione: la funzione restituisce un array!
                $message = "<span style='color:red;'>Errore password: " . htmlspecialchars($pwdResult['reason'], ENT_QUOTES) . "</span>";
                } else {
                try {
                    if (!empty($password)) {
                        // Aggiorna anche la password se è stata fornita
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        $stmt = $pdo->prepare("UPDATE utenti SET username = ?, password = ? WHERE id = ?");
                        $stmt->execute([$username, $hashed_password, $id]);
                    } else {
                        // Aggiorna solo l'username
                        $stmt = $pdo->prepare("UPDATE utenti SET username = ? WHERE id = ?");
                        $stmt->execute([$username, $id]);
                    }
                    $message = "<span style='color:green;'>Utente '" . htmlspecialchars($username) . "' aggiornato con successo!</span>";
                } catch (PDOException $e) {
                    if ($e->getCode() == 23000) {
                        $message = "<span style='color:red;'>Errore: L'username '" . htmlspecialchars($username) . "' esiste già.</span>";
                    } else {
                        $message = "<span style='color:red;'>Errore durante l'aggiornamento dell'utente: " . $e->getMessage() . "</span>";
                    }
                }
            }
        }
        // Logica per l'eliminazione di un utente
        } elseif ($action === 'delete_user') {
            $id = (int)$_POST['user_id'];
            try {
                $stmt = $pdo->prepare("DELETE FROM utenti WHERE id = ?");
                $stmt->execute([$id]);
                $message = "<span style='color:green;'>Utente eliminato con successo!</span>";
            } catch (PDOException $e) {
                $message = "<span style='color:red;'>Errore durante l'eliminazione dell'utente: " . $e->getMessage() . "</span>";
            }
        }
    }
}


// --- Gestione della richiesta di modifica (GET) ---
// Se c'è un parametro 'edit_id' nell'URL, recupera i dati dell'utente per popolare il form di modifica
if (isset($_GET['edit_id'])) {
    $id = (int)$_GET['edit_id'];
    $stmt = $pdo->prepare("SELECT id, username FROM utenti WHERE id = ?");
    $stmt->execute([$id]);
    $edit_user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$edit_user) {
        $message = "<span style='color:red;'>Errore: Utente non trovato per la modifica.</span>";
    }
}

// Recupera tutti gli utenti per la visualizzazione nella tabella
$stmt = $pdo->query("SELECT id, username, created FROM utenti ORDER BY id ASC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Utenti - Area Riservata</title>
    <!-- Collegamento al foglio di stile CSS esterno -->
    <link rel="stylesheet" href="css/gestione_unificata.min.css">
   


<!-- Inclusione dello script JavaScript per la validazione lato CLIENT -->
    <script src="public/js/utility_java.js"></script>
    <script>
        // Funzione per chiedere conferma prima dell'eliminazione
        function confirmDelete(username) {
            return confirm("Sei sicuro di voler eliminare l'utente '" + username + "'?");
        }

        // Funzione per popolare il form di modifica con i dati dell'utente selezionato
        function populateEditForm(id, username) {
            document.getElementById('user_id').value = id;
            document.getElementById('username').value = username;
            document.getElementById('password').value = ''; // La password non viene mostrata per motivi di sicurezza
            document.getElementById('form_action').value = 'update_user';
            document.getElementById('form_submit_button').textContent = 'Aggiorna Utente';
            document.getElementById('form_title').textContent = 'Modifica Utente Esistente';
            document.getElementById('password_note').style.display = 'block';
            document.getElementById('username_validation').style.display = 'block';
            document.getElementById('password_validation').style.display = 'block';
            validateForm(); // Esegue la validazione iniziale
        }

        // Funzione per resettare il form e tornare alla modalità di creazione
        function resetForm() {
            document.getElementById('user_id').value = '';
            document.getElementById('username').value = '';
            document.getElementById('password').value = '';
            document.getElementById('form_action').value = 'create_user';
            document.getElementById('form_submit_button').textContent = 'Crea Utente';
            document.getElementById('form_title').textContent = 'Crea Nuovo Utente';
            document.getElementById('password_note').style.display = 'none';
            document.getElementById('username_validation').style.display = 'none';
            document.getElementById('password_validation').style.display = 'none';
        }

// --- INIZIO: Logica di Validazione LATO CLIENT (Real-time) ---
        // Controlla i campi del form in tempo reale
        function validateForm() {
            const usernameInput = document.getElementById('username');
            const passwordInput = document.getElementById('password');
            const usernameValidation = document.getElementById('username_validation');
            const passwordValidation = document.getElementById('password_validation');

            const isUsernameValid = isValidCellContentClient(usernameInput.value, 3, 50);
            const passwordValidationResult = isPasswordComplexClient(passwordInput.value, 10, 3, 3, 2, 2);
            const isPasswordValid = passwordValidationResult.ok; // Controlla solo lo stato

            // Aggiorna il messaggio di validazione per il campo username
            if (isUsernameValid) {
                usernameValidation.textContent = 'Username valido.';
                usernameValidation.className = 'validation-message valid';
            } else {
                usernameValidation.textContent = 'Username deve avere almeno 3 caratteri.';
                usernameValidation.className = 'validation-message invalid';
            }

            // Aggiorna il messaggio di validazione per il campo password
            const formAction = document.getElementById('form_action').value;
            // La password è obbligatoria solo in creazione o se viene modificata in aggiornamento
            if (passwordInput.value.length > 0 || formAction === 'create_user') {
                 if (isPasswordValid) {
                    passwordValidation.textContent = 'Password valida.';
                    passwordValidation.className = 'validation-message valid';
                } else {
                    // STAMPA IL MOTIVO ESATTO DATO DALLA FUNZIONE
                    passwordValidation.textContent = passwordValidationResult.reason; 
                    passwordValidation.className = 'validation-message invalid';
                }
                
            }
        }
    </script>
</head>
<body onload="resetForm()">
    <header>
        <h1>Gestione Utenti</h1>
        <p>Area Riservata per la gestione degli accessi al Backend.</p>
        <a href="index_FE.php" class="back-link">Torna alla Home</a>
        <a href="logout.php" class="logout-button">Logout</a>
    </header>

    <div class="container">
        <!-- Mostra un messaggio di feedback all'utente se presente -->
        <?php if (!empty($message)): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>

        <!-- Form per la creazione o modifica di un utente -->
        <h2 id="form_title">Crea Nuovo Utente</h2>
        <div class="user-form">
            <form method="POST" action="gestione_utenti.php">
                <input type="hidden" name="action" id="form_action" value="create_user">
                <input type="hidden" name="user_id" id="user_id" value="<?php echo htmlspecialchars($edit_user['id'] ?? ''); ?>">

                <div class="form-group">
                    <label for="username">Nome Utente:</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($edit_user['username'] ?? ''); ?>" onkeyup="validateForm()" required autocomplete="username">
                    <p id="username_validation" class="validation-message"></p>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" placeholder="Minimo 10 caratteri (3 maiuscoli, 3 minuscoli , 2 numeri e 2 speciali)" onkeyup="validateForm()" required autocomplete="current-password">
                    <p id="password_validation" class="validation-message"></p>
                    <small id="password_note" style="display:none; color:#6c757d;">Lascia vuoto per non modificare la password in fase di aggiornamento.</small>
                </div>
                <button type="submit" id="form_submit_button">Crea Utente</button>
                <button type="reset" onclick="resetForm()">Annulla/Nuovo</button>
            </form>
        </div>

        <!-- Tabella per la visualizzazione e gestione degli utenti -->
        <h2>Elenco Utenti</h2>
        <?php if (count($users) > 0): ?>
            <table class="user-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome Utente</th>
                        <th>Creato il</th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['id']); ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['created']); ?></td>
                            <td class="action-buttons">
                                <a href="javascript:void(0);" onclick="populateEditForm(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>')" class="edit-btn">Modifica</a>
                                <form method="POST" style="display:inline;" onsubmit="return confirmDelete('<?php echo htmlspecialchars($user['username']); ?>');">
                                    <input type="hidden" name="action" value="delete_user">
                                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id']); ?>">
                                    <button type="submit" class="delete-btn">Cancella</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="text-align: center;">Nessun utente registrato.</p>
        <?php endif; ?>
    </div>

    <!-- Footer della pagina -->
    <footer>
        <p>&copy; 2025 Il Tuo Sito. Tutti i diritti riservati.</p>
    </footer>
</body>
</html>
