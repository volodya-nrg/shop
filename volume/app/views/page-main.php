<div class="main">
    <!--        <div class="main_column sx-side">-->
    <!--            Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ad adipisci aut autem est iste maiores ntem-->
    <!--            voluptates.-->
    <!--        </div>-->
    <div class="main_column">
        <div>
            Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ad adipisci aut autem est iste maiores ntem
            voluptates.
        </div>
        <div>
            <h1>Lorem ipsum dolor sit amet, consectetur adipisicing elit</h1>
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ad adipisci aut autem est iste maiores ntem
                voluptates.</p>
            <?php
            for ($i = 0; $i < 6; $i++) {
                echo template(EnumViewFile::ModuleItem);
            }
            ?>
        </div>
    </div>
    <!--        <div class="main_column sx-side">-->
    <!--            Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ad adipisci aut autem est iste maiores ntem-->
    <!--            voluptates.-->
    <!--        </div>-->
</div>