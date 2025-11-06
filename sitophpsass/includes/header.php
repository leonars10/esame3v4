<?php
function renderHeader($site_name, $menu_items) {
?>

<header>
    <div class="header-content">
        <!-- Logo -->
        <div class="header-logo">
            <a href="index.php"><img src="Img/Logo_Sito.png" alt="Logo del sito" title="Home"></a>
        </div>

        <input type="checkbox" id="menu-toggle" class="menu-toggle">
        <label for="menu-toggle" class="hamburger-menu" title="Menù">
            <span></span>
            <span></span>
            <span></span>
            <span></span>
        </label>

        <!-- Menu di navigazione -->
        <nav class="menu">
            <ul>
                <?php foreach ($menu_items as $name => $link): ?>
                    <li><a href="<?= htmlspecialchars($link); ?>"><?= htmlspecialchars($name); ?></a></li>
                <?php endforeach; ?>
            </ul>
        </nav>
    </div>
</header>
<?php
}
?>

