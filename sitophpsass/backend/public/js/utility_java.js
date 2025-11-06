/**
 * Utility JS condiviso per l'app di backend
 * - Validazione client
 * - Funzioni di preview immagine centralizzate (inizializzazione e set da path)
 */

// Lista caratteri speciali (stessa stringa del server)
const ALLOWED_SPECIALS = '!@#$%^&*()-_=+[]{}|;:,.<>/?`~';

// Escapa per costruire dinamicamente regex sicure
function escapeForRegex(s) {
    return s.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
}

/**
 * Controlla il contenuto di una stringa per verificare che non sia vuoto
 * e che rispetti una lunghezza minima e massima.
 * @param {string} content
 * @param {number} minLength
 * @param {number} maxLength
 * @returns {boolean}
 */
function isValidCellContentClient(content, minLength, maxLength) {
    const trimmedContent = String(content || '').trim();
    if (trimmedContent.length === 0) return false;
    const contentLength = trimmedContent.length;
    return contentLength >= minLength && contentLength <= maxLength;
}

//// Controlla complessità password lato CLIENT
// Restituisce { ok: boolean, reason: string }
function isPasswordComplexClient(pwd, minLen = 10, minUpper = 3, minLower = 3, minDigits = 2, minSpecial = 2, allowedSpecials = ALLOWED_SPECIALS) {
    if (!pwd || pwd.length < minLen) {
        return { ok: false, reason: 'Lunghezza minima: ' + minLen + ' caratteri.' };
    }
    const upperCount = (pwd.match(/[A-Z]/g) || []).length;
    if (upperCount < minUpper) {
        return { ok: false, reason: 'Servono almeno ' + minUpper + ' lettere maiuscole.' };
    }
    const lowerCount = (pwd.match(/[a-z]/g) || []).length;
    if (lowerCount < minLower) {
        return { ok: false, reason: 'Servono almeno ' + minLower + ' lettere minuscole.' };
    }
    const digitCount = (pwd.match(/[0-9]/g) || []).length;
    if (digitCount < minDigits) {
        return { ok: false, reason: 'Servono almeno ' + minDigits + ' cifre.' };
    }
    const allowedSpecialsRegex = new RegExp('[' + escapeForRegex(allowedSpecials) + ']', 'g');
    const specialMatches = (pwd.match(allowedSpecialsRegex) || []);
    if (specialMatches.length < minSpecial) {
        return { ok: false, reason: 'Servono almeno ' + minSpecial + ' caratteri speciali.' };
    }
    const forbiddenRegex = new RegExp('[^a-zA-Z0-9\\s' + escapeForRegex(allowedSpecials) + ']', 'g');
    if (forbiddenRegex.test(pwd)) {
        return { ok: false, reason: 'La password contiene caratteri speciali non consentiti. Caratteri consentiti: ' + allowedSpecials };
    }
    return { ok: true, reason: '' };
}
window.isPasswordComplexClient = isPasswordComplexClient;
window.isValidCellContentClient = isValidCellContentClient;

/**
 * Inizializza il comportamento di preview per un input file.
 * Parametri:
 *  - fileInputId: id dell'input type=file
 *  - previewImgId: id dell'elemento <img> per la preview
 *  - filenameElemId: id dell'elemento (p, span) dove mostrare il nome file
 *  - allowedExtensions: array di estensioni senza punto, es ['jpg','png']
 *  - noteElemId (opzionale): id dell'elemento small/note da mostrare in update
 *  - currentPathElemId (opzionale): id dell'elemento che mostra il path corrente
 *
 * Comportamento:
 *  - quando si seleziona un file valido mostra la preview (dataURL) e scrive il nome del file
 *  - se formato non consentito pulisce l'input e mostra alert
 */
function initImagePreview(fileInputId, previewImgId, filenameElemId, allowedExtensions, noteElemId = null, currentPathElemId = null) {
    const fileInput = document.getElementById(fileInputId);
    const preview = document.getElementById(previewImgId);
    const filenameElem = filenameElemId ? document.getElementById(filenameElemId) : null;
    const noteElem = noteElemId ? document.getElementById(noteElemId) : null;
    const currentPathElem = currentPathElemId ? document.getElementById(currentPathElemId) : null;

    if (!fileInput) return;

    fileInput.addEventListener('change', function () {
        const f = this.files[0];
        if (!f) {
            if (preview) preview.style.display = 'none';
            if (filenameElem) filenameElem.textContent = 'Nessuna immagine selezionata.';
            return;
        }
        const ext = (f.name.split('.').pop() || '').toLowerCase();
        if (!Array.isArray(allowedExtensions) || !allowedExtensions.map(e => e.toLowerCase()).includes(ext)) {
            alert('Formato file non consentito (' + ext + '). Estensioni consentite: ' + (allowedExtensions || []).join(', '));
            this.value = '';
            if (preview) preview.style.display = 'none';
            if (filenameElem) filenameElem.textContent = 'Nessuna immagine selezionata.';
            return;
        }
        const reader = new FileReader();
        reader.onload = function (e) {
            if (preview) { preview.src = e.target.result; preview.style.display = 'block'; }
            if (filenameElem) filenameElem.textContent = f.name;
            if (noteElem) noteElem.style.display = 'none';
            if (currentPathElem) currentPathElem.style.display = 'none';
        };
        reader.readAsDataURL(f);
    });
}

/**
 * Imposta la preview da un path già presente (es. path salvato nel DB)
 * - previewImgId: id <img>
 * - path: percorso web relativo/assoluto da usare come src
 * - filenameElemId / currentPathElemId / noteElemId opzionali
 */
function setPreviewFromPath(previewImgId, path, filenameElemId = null, currentPathElemId = null, noteElemId = null) {
    const preview = document.getElementById(previewImgId);
    const filenameElem = filenameElemId ? document.getElementById(filenameElemId) : null;
    const currentPathElem = currentPathElemId ? document.getElementById(currentPathElemId) : null;
    const noteElem = noteElemId ? document.getElementById(noteElemId) : null;

    if (!path) {
        if (preview) { preview.src = ''; preview.style.display = 'none'; }
        if (filenameElem) filenameElem.textContent = 'Nessuna immagine selezionata.';
        if (currentPathElem) currentPathElem.style.display = 'none';
        if (noteElem) noteElem.style.display = 'none';
        return;
    }

    if (preview) {
        preview.src = path;
        preview.style.display = 'block';
    }
    if (filenameElem) filenameElem.textContent = path.split('/').pop();
    if (currentPathElem) {
        currentPathElem.textContent = 'Percorso attuale: ' + path;
        currentPathElem.style.display = 'block';
    }
    if (noteElem) noteElem.style.display = 'block';
}

// Esporta le funzioni nello scope globale per compatibilità con codice inline
window.initImagePreview = initImagePreview;
window.setPreviewFromPath = setPreviewFromPath;