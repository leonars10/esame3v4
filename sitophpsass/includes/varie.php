



    <form id="contactForm" class="contact-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" novalidate>
        <div class="form-group">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($nome); ?>">
            <span class="errore-js" id="errore_nome_js"></span>
            <span class="errore-php"><?php echo $errore_nome_php; ?></span>
        </div>

        

    <form id="contactForm" class="contact-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" novalidate>
        <div class="form-group" style="display: flex; flex-direction: column; align-items: flex-start;">
            <label for="nome" style="align-self: flex-start; margin-bottom: 4px;">Nome:</label>
            <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($nome); ?>">
            <span class="errore-js" id="errore_nome_js"></span>
            <span class="errore-php"><?php echo $errore_nome_php; ?></span>
        </div>

