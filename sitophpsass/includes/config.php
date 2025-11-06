<?php
$site_name = "Leonardo Cipollini";
$current_year = date("Y");

// Menu di navigazione
$menu_items = [
    "Servizi" => "servizi.php",
    "Portfolio" => "lavori.php",
    "Chi sono" => "contatti.php",
    "Area riservata" => "backend/login.php"
    
];

// file CSS
$base_css_path = "Css/";
$index_css = $base_css_path . "indexstile.min.css"; 
$lavori_css = $base_css_path . "lavoristile.min.css"; 
$contatti_css = $base_css_path . "contattistile.min.css"; 
$dettaglilavoro_css = $base_css_path . "dettagliolavorostile.min.css"; 
$servizi_css = $base_css_path . "servizistile.min.css"; 

function includeCSS($css_files) {
    foreach ((array) $css_files as $css_file) {
        if (file_exists($css_file)) {
            echo '<link rel="stylesheet" href="' . htmlspecialchars($css_file) . '">' . PHP_EOL;
        } else {
            echo '<!-- CSS non trovato: ' . htmlspecialchars($css_file) . ' -->' . PHP_EOL;
        }
    }
}


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

?>




