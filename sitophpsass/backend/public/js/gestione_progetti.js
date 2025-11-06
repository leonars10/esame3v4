// JS specifico per gestione_progetti.php (esterno, nessun JS inline funzionale qui)
// Richiede utility_java.js (initImagePreview, setPreviewFromPath, isValidCellContentClient)

const ALLOWED = window.ALLOWED_EXTENSIONS_CLIENT || ['jpg','jpeg','png','gif','webp','avif'];

function validateProgettoForm() {
    const nome = document.getElementById('progetto_nome_progetto').value;
    const descr = document.getElementById('progetto_descrizione').value;
    const tipo = document.getElementById('progetto_tipo').value;
    const fileInput = document.getElementById('progetto_src_immagine');
    const altImg = document.getElementById('progetto_alt_immagine_principale').value;
    const action = document.getElementById('progetto_form_action').value;
    let msg = '';
    if (!isValidCellContentClient(nome,3,100)) msg += '- Nome Progetto 3-100\n';
    if (!isValidCellContentClient(descr,10,500)) msg += '- Descrizione 10-500\n';
    if (!isValidCellContentClient(tipo,2,50)) msg += '- Tipo 2-50\n';
    if (action === 'create_progetto' && fileInput && fileInput.files.length === 0) msg += '- Seleziona file immagine principale\n';
    if (!isValidCellContentClient(altImg,3,100)) msg += '- ALT Immagine 3-100\n';
    if (msg) { alert('Correggi:\n' + msg); return false; }
    return true;
}

function validateImmagineForm() {
    const idproj = document.getElementById('immagine_id_progetto').value;
    const alt = document.getElementById('immagine_alt_immagine').value;
    const fileInput = document.getElementById('immagine_src_immagine');
    const action = document.getElementById('immagine_form_action').value;
    let msg = '';
    if (!idproj) msg += '- Seleziona progetto\n';
    if (!isValidCellContentClient(alt,5,255)) msg += '- ALT Immagine 5-255\n';
    if (action === 'create_immagine' && fileInput.files.length === 0) msg += '- Seleziona file immagine\n';
    if (msg) { alert('Correggi:\n' + msg); return false; }
    return true;
}

// populateProgettoForm signature: id, alt_img_princ, descr, tipo, src_img_princ, nome
function populateProgettoForm(id, alt_img_princ, descr, tipo, src_img_princ, nome) {
    document.getElementById('progetto_id').value = id || '';
    document.getElementById('progetto_nome_progetto').value = nome || '';
    document.getElementById('progetto_descrizione').value = descr || '';
    document.getElementById('progetto_tipo').value = tipo || '';
    document.getElementById('progetto_alt_immagine_principale').value = alt_img_princ || '';
    document.getElementById('progetto_form_action').value = 'update_progetto';
    document.getElementById('progetto_submit_button').textContent = 'Aggiorna Progetto';

    // preview della immagine principale
    if (src_img_princ && typeof setPreviewFromPath === 'function') {
        const projImgPath = src_img_princ.startsWith('../') ? src_img_princ : '../' + src_img_princ;
        setPreviewFromPath('progetto_image_preview', projImgPath, 'progetto_preview_filename', 'progetto_current_src_path', 'progetto_file_input_note');
        const oldInput = document.getElementById('progetto_old_immagine_principale');
        if (oldInput) oldInput.value = src_img_princ;
    } else {
        if (typeof setPreviewFromPath === 'function') setPreviewFromPath('progetto_image_preview', '');
        const oldInput = document.getElementById('progetto_old_immagine_principale');
        if (oldInput) oldInput.value = '';
    }

    const fileInput = document.getElementById('progetto_src_immagine');
    if (fileInput) fileInput.required = false;
}

function resetProgettoForm() {
    document.getElementById('progetto_form').reset();
    document.getElementById('progetto_form_action').value = 'create_progetto';
    document.getElementById('progetto_submit_button').textContent = 'Crea Progetto';
    document.getElementById('progetto_id').value = '';
    document.getElementById('progetto_old_immagine_principale').value = '';
    if (typeof setPreviewFromPath === 'function') setPreviewFromPath('progetto_image_preview', '');
    const fileInput = document.getElementById('progetto_src_immagine');
    if (fileInput) fileInput.required = true;
    document.getElementById('progetto_nome_progetto').value = '';
    document.getElementById('progetto_alt_immagine_principale').value = '';
}

// Immagine lavoro helpers
function populateImmagineForm(id_lavoro, id_progetto, src_path, alt) {
    document.getElementById('immagine_id_lavoro').value = id_lavoro || '';
    document.getElementById('immagine_id_progetto').value = id_progetto || '';
    document.getElementById('immagine_alt_immagine').value = alt || '';
    document.getElementById('immagine_form_action').value = 'update_immagine';
    document.getElementById('immagine_submit_button').textContent = 'Aggiorna Immagine';
    if (src_path && typeof setPreviewFromPath === 'function') {
        const p = src_path.startsWith('../') ? src_path : '../' + src_path;
        setPreviewFromPath('image_preview', p, 'preview_filename', 'current_src_path', 'file_input_note');
    }
}

function resetImmagineForm() {
    document.getElementById('immagine_form').reset();
    document.getElementById('immagine_id_lavoro').value = '';
    if (typeof setPreviewFromPath === 'function') setPreviewFromPath('image_preview', '');
    document.getElementById('immagine_form_action').value = 'create_immagine';
    document.getElementById('immagine_submit_button').textContent = 'Crea Immagine';
}

document.addEventListener('DOMContentLoaded', function () {
    resetProgettoForm();
    resetImmagineForm();

    if (typeof initImagePreview === 'function') {
        initImagePreview('immagine_src_immagine', 'image_preview', 'preview_filename', ALLOWED, 'file_input_note', 'current_src_path');
        initImagePreview('progetto_src_immagine', 'progetto_image_preview', 'progetto_preview_filename', ALLOWED, 'progetto_file_input_note', 'progetto_current_src_path');
    }

    if (window.__EDIT_PROGETTO) {
        const e = window.__EDIT_PROGETTO;
        populateProgettoForm(e.id_progetto, e.alt_immagine_principale || '', e.descrizione || '', e.tipo || '', e.src_immagine_principale || '', e.nome_progetto || '');
    }
    if (window.__EDIT_IMMAGINE) {
        const im = window.__EDIT_IMMAGINE;
        populateImmagineForm(im.id_lavoro || '', im.id_progetto || '', im.src_immagine || '', im.alt_immagine || '');
    }
});

// Esponi funzioni nello scope globale (usate dagli onclick inline "Modifica")
window.populateProgettoForm = populateProgettoForm;
window.resetProgettoForm = resetProgettoForm;
window.populateImmagineForm = populateImmagineForm;
window.resetImmagineForm = resetImmagineForm;
window.validateProgettoForm = validateProgettoForm;
window.validateImmagineForm = validateImmagineForm;