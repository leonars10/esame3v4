<?php



/**
 * Controlla se la sessione di login è attiva.
 * Se non lo è, reindirizza alla pagina di login e termina lo script.
 */
function requireLoggedIn() {
    // La funzione assume che session_start() sia già stata chiamata.
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        // Usa una funzione che termina lo script dopo il reindirizzamento.
        header("Location: login.php");
        exit();
    }
}

/**
 * Pulisce e valida una stringa in input.
 * Esegue trim, strip_tags, e htmlspecialchars.
 *
 * @param string $data La stringa da pulire.
 * @return string La stringa pulita.
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data); // Rimuove gli slash aggiunti da addslashes()
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8'); // Converte caratteri speciali in entità HTML
    return $data;
}





/**
 * Controlla se una stringa è valida (non vuota e rientra in un range di lunghezza tra minLength and maxLength).
 * @param string $content Il contenuto da validare.
 * @param int $minLength La lunghezza minima richiesta.
 * @param int $maxLength La lunghezza massima consentita.
 * @return bool True se il contenuto è valido, false altrimenti.
 */
function isValidCellContent($content, $minLength, $maxLength) {
    $trimmedContent = trim($content);
    if (empty($trimmedContent)) {
        return false;
    }
    $contentLength = strlen($trimmedContent);
    return $contentLength >= $minLength && $contentLength <= $maxLength;
}

// Lista caratteri speciali (stessa stringa del server)
if (!defined('ALLOWED_SPECIALS')) {
    define('ALLOWED_SPECIALS', '!@#$%^&*()-_=+[]{}|;:,.<>/?`~');
}

// Escapa per costruire dinamicamente regex sicure
function escapeForRegex($s) {
    return preg_quote($s, '/');
}

// Controlla complessità password lato SERVER
// Restituisce array('ok' => bool, 'reason' => string)
// Aggiunti parametri per minuscole e numeri
function validatePasswordComplexityServer($pwd, $minLen = 10, $minUpper = 3, $minLower = 3, $minDigits = 2, $minSpecial = 2, $allowedSpecials = ALLOWED_SPECIALS) {
    $pwd = (string)$pwd;
    if (trim($pwd) === '' || mb_strlen($pwd) < $minLen) {
        return array('ok' => false, 'reason' => 'Lunghezza minima: ' . $minLen . ' caratteri.');
    }

    $upperCount = preg_match_all('/[A-Z]/u', $pwd, $matchesUpper) ?: 0;
    if ($upperCount < $minUpper) {
        return array('ok' => false, 'reason' => 'Servono almeno ' . $minUpper . ' lettere maiuscole.');
    }

    $lowerCount = preg_match_all('/[a-z]/u', $pwd, $matchesLower) ?: 0;
    if ($lowerCount < $minLower) {
        return array('ok' => false, 'reason' => 'Servono almeno ' . $minLower . ' lettere minuscole.');
    }

    $digitCount = preg_match_all('/\d/u', $pwd, $matchesDigit) ?: 0;
    if ($digitCount < $minDigits) {
        return array('ok' => false, 'reason' => 'Servono almeno ' . $minDigits . ' numeri.');
    }

    $specialCount = preg_match_all('/[^a-zA-Z0-9\s]/u', $pwd, $matchesSpecial) ?: 0;
    if ($specialCount < $minSpecial) {
        return array('ok' => false, 'reason' => 'Servono almeno ' . $minSpecial . ' caratteri speciali.');
    }

    $allowedEscaped = escapeForRegex($allowedSpecials);
    $forbiddenPattern = '/[^a-zA-Z0-9\s' . $allowedEscaped . ']/u';
    if (preg_match($forbiddenPattern, $pwd)) {
        return array('ok' => false, 'reason' => 'La password contiene caratteri speciali non consentiti. Caratteri consentiti: ' . $allowedSpecials);
    }

    return array('ok' => true, 'reason' => '');
}

// Mantieni la funzione per l'uso lato server nel validateForm già presente in gestione_utenti.php
// esempio: $result = validatePasswordComplexityServer($password);


// Funzione upload immagini
function handleImageUpload($file_input_name, $old_file_path = null) {
    global $message;

    if (!isset($_FILES[$file_input_name]) || $_FILES[$file_input_name]['error'] === UPLOAD_ERR_NO_FILE) {
        return ['success' => true, 'path' => $old_file_path];
    }

    $file = $_FILES[$file_input_name];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $message = "<span style='color:red;'>Errore di caricamento: " . $file['error'] . "</span>";
        return ['success' => false];
    }

    if ($file['size'] > MAX_FILE_SIZE) {
        $message = "<span style='color:red;'>Errore: file troppo grande (max " . (MAX_FILE_SIZE / (1024*1024)) . " MB).</span>";
        return ['success' => false];
    }

    $file_info = pathinfo($file['name']);
    $ext = strtolower($file_info['extension'] ?? '');
    if (!in_array($ext, ALLOWED_EXTENSIONS)) {
        $message = "<span style='color:red;'>Errore: formato file non consentito.</span>";
        return ['success' => false];
    }

    if (!is_dir(UPLOAD_DIR_FS) && !mkdir(UPLOAD_DIR_FS, 0755, true)) {
        $message = "<span style='color:red;'>Errore: impossibile creare cartella upload.</span>";
        return ['success' => false];
    }

    $new_name = uniqid('img_', true) . '.' . $ext;
    $dest_fs = UPLOAD_DIR_FS . $new_name;
    $web_path = UPLOAD_DIR_WEB . $new_name;

    if (!move_uploaded_file($file['tmp_name'], $dest_fs)) {
        $message = "<span style='color:red;'>Errore durante lo spostamento del file. Controllare permessi.</span>";
        return ['success' => false];
    }

    if ($old_file_path) {
        $old_fs = UPLOAD_DIR_FS . basename($old_file_path);
        if (file_exists($old_fs) && $old_fs !== $dest_fs) {
            @unlink($old_fs);
        }
    }

    return ['success' => true, 'path' => $web_path];
}




?>
   
    
