<h1>Lorem ipsum dolor sit amet, consectetur adipisicing elit</h1>
<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ad adipisci aut autem est iste maiores ntem voluptates.</p>
<div>
    <?php
    for ($i=0; $i < 6; $i++) {
        echo template(DIR_VIEWS . "/" . ViewModuleItem, []);
    }
    ?>
</div>