<?php

// Definisci le variabili e impostale a valori vuoti
$nome = $cognome = $email = $cellulare = $servizi = $oggetto = $messaggio = "";
$errore_nome_php = $errore_cognome_php = $errore_cellulare_php = $errore_email_php = $errore_servizi_php = $errore_oggetto_php = $errore_messaggio_php = "";
$messaggio_successo = "";

// Processa i dati del form solo quando il form viene inviato (VALIDAZIONE SERVER-SIDE)
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validazione Nome
    if (empty(trim($_POST["nome"]))) {
        $errore_nome_php = "Il nome è obbligatorio (validazione server).";
    } else {
        $nome = trim($_POST["nome"]);
    }

    // Validazione Cognome
    if (empty(trim($_POST["cognome"]))) {
        $errore_cognome_php = "Il cognome è obbligatorio (validazione server).";
    } else {
        $cognome = trim($_POST["cognome"]);
    }

    // Validazione Email
    if (empty(trim($_POST["email"]))) {
        $errore_email_php = "L'email è obbligatoria (validazione server).";
    } else {
        $email_input = trim($_POST["email"]);
        if (!filter_var($email_input, FILTER_VALIDATE_EMAIL)) {
            $errore_email_php = "Formato email non valido (validazione server).";
        } else {
            $email = $email_input;
        }
    }

    // Validazione Cellulare
    if (empty(trim($_POST["cellulare"]))) {
       // $errore_cellulare_php = "Il cellulare è obbligatorio (validazione server).";
    } else {
        $cellulare_input = trim($_POST["cellulare"]);
        if (!ctype_digit($cellulare_input)) {
            $errore_cellulare_php = "Il cellulare deve contenere solo numeri (validazione server).";
        } elseif (strlen($cellulare_input) < 8) {
            $errore_cellulare_php = "Il cellulare deve essere di almeno 8 cifre (validazione server).";
        } else {
            $cellulare = $cellulare_input;
        }
    }

    // Validazione Servizi
    if (empty($_POST["servizi"])) {
        $errore_servizi_php = "Seleziona almeno un servizio (validazione server).";
    } else {
        $servizi = $_POST["servizi"];
        // Puoi anche fare un controllo qui per assicurarti che i servizi siano validi
    }

    // Validazione Oggetto
    if (empty(trim($_POST["oggetto"]))) {
        $errore_oggetto_php = "L'oggetto è obbligatorio (validazione server).";
    } else {
        $oggetto = trim($_POST["oggetto"]);
    }

    // Validazione Messaggio
    if (empty(trim($_POST["messaggio"]))) {
        $errore_messaggio_php = "Il messaggio è obbligatorio (validazione server).";
    } else {
        $messaggio_input = trim($_POST["messaggio"]);
        $lunghezza_messaggio = strlen($messaggio_input);
        if ($lunghezza_messaggio <= 50) {
            $errore_messaggio_php = "Il messaggio deve contenere più di 50 caratteri (validazione server).";
        } elseif ($lunghezza_messaggio > 1000) {
            $errore_messaggio_php = "Il messaggio non può superare i 1000 caratteri (validazione server).";
        } else {
            $messaggio = $messaggio_input;
        }
    }


            // Controlla se ci sono errori prima di processare i dati
if (
    empty($errore_nome_php) &&
    empty($errore_cognome_php) &&
    empty($errore_email_php) &&
    empty($errore_cellulare_php) &&
    empty($errore_servizi_php) &&
    empty($errore_oggetto_php) &&
    empty($errore_messaggio_php)
) {
    $messaggio_successo = "Messaggio inviato con successo!";
    // Salva i dati in un file di testo (opzionale)
    $file_path = dirname(__DIR__) . '/messaggi_contatti.txt'; // Percorso del file di testo nella directory superiore
    $contenuto = "Nome: $nome\nCognome: $cognome\nEmail: $email\nCellulare: $cellulare\nServizi: $servizi\nOggetto: $oggetto\nMessaggio:\n$messaggio\n--------------------\n";
    // Salva il contenuto nel file, aggiungendo alla fine del file esistente
    file_put_contents($file_path, $contenuto, FILE_APPEND);
    $nome = $cognome = $email = $cellulare = $servizi = $oggetto = $messaggio = ""; // Resetta i campi dopo l'invio PHP
} else {
    // Se il form non è stato inviato, resetta i campi
    $nome = $cognome = $email = $cellulare = $servizi = $oggetto = $messaggio = "";
    $errore_nome_php = $errore_cognome_php = $errore_cellulare_php = $errore_email_php = $errore_servizi_php = $errore_oggetto_php = $errore_messaggio_php = "";
    $messaggio_successo = ""; }



}
?>




    <h2>Contattami</h2>
<div class="scritte-form">  
    <p>Compila il form per inviare la tua richiesta</p>
</div>

<div class="contact-form-contain">

    <form id="contactForm" class="contact-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" novalidate>
        <div class="form-group">
            <label for="nome">Nome:*</label>
            <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($nome); ?>">
            <span class="errore-js" id="errore_nome_js"></span>
            <span class="errore-php"><?php echo $errore_nome_php; ?></span>
        </div>

        <div class="form-group">
            <label for="cognome">Cognome:*</label>
            <input type="text" id="cognome" name="cognome" value="<?php echo htmlspecialchars($cognome); ?>">
            <span class="errore-js" id="errore_cognome_js"></span>
            <span class="errore-php"><?php echo $errore_cognome_php; ?></span>
        </div>

        <div class="form-group">
            <label for="email">Email:*</label>
            <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>">
            <span class="errore-js" id="errore_email_js"></span>
            <span class="errore-php"><?php echo $errore_email_php; ?></span>
        </div> 

        <div class="form-group">
            <label for="cellulare">Cellulare:</label>
            <input type="text" id="cellulare" name="cellulare" value="<?php echo htmlspecialchars($cellulare); ?>">
            <span class="errore-js" id="errore_cellulare_js"></span>
            <span class="errore-php"><?php echo $errore_cellulare_php; ?></span>
        </div>

        <div class="form-row">
        <label>Servizi:*</label>
        <input type="checkbox" name="servizi" value="BRAND DESIGN"> BRAND DESIGN<br>
        <input type="checkbox" name="servizi" value="MOTION DESIGN"> MOTION DESIGN<br>
        <input type="checkbox" name="servizi" value="LAYOUT DESIGN"> LAYOUT DESIGN<br>
        <input type="checkbox" name="servizi" value="WEB"> WEB
        <span class="errore-js" id="errore_servizi_js"></span>
        <span class="errore-php"><?php echo $errore_servizi_php; ?></span>
        </div>

        <div class="form-group">
            <label for="oggetto">Oggetto:*</label>
            <input type="text" id="oggetto" name="oggetto" value="<?php echo htmlspecialchars($oggetto); ?>">
            <span class="errore-js" id="errore_oggetto_js"></span>
            <span class="errore-php"><?php echo $errore_oggetto_php; ?></span>
        </div>

        <div class="form-group">
            <label for="messaggio">Messaggio:* (max 1000 caratteri)</label>
            <textarea id="messaggio" name="messaggio" maxlength="1000"><?php echo htmlspecialchars($messaggio); ?></textarea>
            <span class="errore-js" id="errore_messaggio_js"></span>
            <span class="errore-php"><?php echo $errore_messaggio_php; ?></span>
        </div>

        <div class="form-group">
            <input type="submit" value="Invia Messaggio">
        </div>

        <?php if (!empty($messaggio_successo)): ?>
        <div class="successo"><?php echo $messaggio_successo; ?></div>
         <?php endif; ?>

         
        <div class="obbligo">* Campi obbligatori</div>

        </div> 


    
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('contactForm');
    const nomeInput = document.getElementById('nome');
    const cognomeInput = document.getElementById('cognome');
    const emailInput = document.getElementById('email');
    const cellulareInput = document.getElementById('cellulare');
    const oggettoInput = document.getElementById('oggetto');
    const messaggioInput = document.getElementById('messaggio');
    // RIMOSSO: const serviziInput = Array.from(document.querySelectorAll("input[name='servizi']:checked")).map(el => el.value);
    
    

    const erroreNomeJs = document.getElementById('errore_nome_js');
    const erroreCognomeJs = document.getElementById('errore_cognome_js');
    const erroreEmailJs = document.getElementById('errore_email_js');
    const erroreCellulareJs = document.getElementById('errore_cellulare_js');
    const erroreOggettoJs = document.getElementById('errore_oggetto_js');
    const erroreMessaggioJs = document.getElementById('errore_messaggio_js');
    const erroreServiziJs = document.getElementById('errore_servizi_js');

    // Contatore caratteri per il messaggio
    const charCounter = document.createElement('div');
    charCounter.id = 'charCount';
    //charCounter.style.color = '#ff0000'; // Colore del contatore
    messaggioInput.insertAdjacentElement('afterend', charCounter);
    messaggioInput.addEventListener('input', function () {
        const currentLength = this.value.length;
        const maxLength = this.getAttribute('maxlength');
        charCounter.textContent = `Caratteri utilizzati: ${currentLength}/${maxLength}`;
    });
    // Inizializza il contatore
    const initialLength = messaggioInput.value.length;
    const maxLength = messaggioInput.getAttribute('maxlength');
    charCounter.textContent = `Caratteri utilizzati: ${initialLength}/${maxLength}`;
    // Validazione JavaScript
    // Aggiungi un listener per l'evento submit del form

   

    form.addEventListener('submit', function (event) {
        let isValid = true;

        // Reset errori JavaScript precedenti
        erroreNomeJs.textContent = '';
        erroreCognomeJs.textContent = '';
        erroreEmailJs.textContent = '';
        erroreCellulareJs.textContent = '';
        erroreOggettoJs.textContent = '';
        erroreMessaggioJs.textContent = '';
        erroreServiziJs.textContent = '';

        // Validazione Nome
        if (nomeInput.value.trim() === '') {
            erroreNomeJs.textContent = 'Il nome è obbligatorio.';
            isValid = false;
        }

        // Validazione Cognome
        if (cognomeInput.value.trim() === '') {
            erroreCognomeJs.textContent = 'Il cognome è obbligatorio.';
            isValid = false;
        }

        // Validazione Email
        const emailVal = emailInput.value.trim();
        if (emailVal === '') {
            erroreEmailJs.textContent = 'L\'email è obbligatoria.';
            isValid = false;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailVal)) { // Controlla il formato dell'email
            erroreEmailJs.textContent = 'Formato email non valido.';
            isValid = false;
        }
  

        // Validazione Cellulare
        const cellulareVal = cellulareInput.value.trim();
        if (cellulareVal === '') {
          //  erroreCellulareJs.textContent = 'Il cellulare è obbligatorio.';
          //  isValid = false;
        } else if (!/^\d+$/.test(cellulareVal)) { // Controlla se sono solo numeri
            erroreCellulareJs.textContent = 'Il cellulare deve contenere solo numeri.';
            isValid = false;
        } else if (cellulareVal.length < 8) {
            erroreCellulareJs.textContent = 'Il cellulare deve essere di almeno 8 cifre.';
            isValid = false;
        }

        // Validazione Servizi
        const serviziInput = Array.from(document.querySelectorAll("input[name='servizi']:checked")).map(el => el.value);
        if (serviziInput.length === 0) {
            erroreServiziJs.textContent = 'Seleziona almeno un servizio.';
            isValid = false;
        } else {
            erroreServiziJs.textContent = ''; // Reset errore se i servizi sono selezionati
        }

        // Validazione Oggetto
        const oggettoVal = oggettoInput.value.trim();
        if (oggettoVal === '') {
            erroreOggettoJs.textContent = 'L\'oggetto è obbligatorio.';
            isValid = false;
        }

        // Validazione Messaggio
        const messaggioVal = messaggioInput.value.trim();
        const lunghezzaMessaggio = messaggioVal.length;
        if (messaggioVal === '') {
            erroreMessaggioJs.textContent = 'Il messaggio è obbligatorio.';
            isValid = false;
        } else if (lunghezzaMessaggio <= 50) {
            erroreMessaggioJs.textContent = 'Il messaggio deve contenere più di 50 caratteri.';
            isValid = false;
        } else if (lunghezzaMessaggio > 1000) {
            erroreMessaggioJs.textContent = 'Il messaggio non può superare i 1000 caratteri.';
            isValid = false;
        }

        if (!isValid) {
            event.preventDefault(); // Impedisce l'invio del form se ci sono errori JS
   
            // Mantieni il focus sul form
            form.scrollIntoView({ behavior: "smooth", block: "start" });
        } else {
            // Se il form è valido, puoi inviarlo normalmente
            // Se vuoi fare un'ulteriore validazione lato server, puoi farlo qui
            // form.submit(); // Invia il form se necessario
            
        }
        // Se isValid è true, il form verrà inviato normalmente e la validazione PHP entrerà in gioco.
         //else {      
            // Se non valido, mantieni il focus sul form
        //    form.scrollIntoView({ behavior: "auto", block: "start" });
        //    event.preventDefault();
        //}
        
    });
});
</script>

</body>

