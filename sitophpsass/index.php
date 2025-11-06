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
    <?php includeCSS($index_css); ?> 
</head>

<body>

<?php renderHeader($site_name, $menu_items); ?>

<div class="sfondo">
    <img src="Css/Sfondo/Grafica_Sito.png" alt="Grafica-sito">
</div>


<?php include 'includes/contact_form.php'; ?>




<?php renderFooter($current_year, $site_name); ?>

</body>
</html>
