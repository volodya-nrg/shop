<?php
$error = $__data[EnumField::Error->value] ?? "";
?>
<div class="main">
    <div class="main_column sx-side">
        <?php
        echo template(EnumViewFile::ModuleCatalogMenu);
        ?>
    </div>
    <div class="main_column">
        <div>
            <?php
            echo template(EnumViewFile::ModuleBreakCrumbs);
            ?>
        </div>
        <div>
            <h1>Lorem ipsum dolor sit amet, consectetur adipisicing elit</h1>
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ad adipisci aut autem est iste maiores ntem
                voluptates.</p>
            <div>
                <?php
                for ($i = 0; $i < 3; $i++) {
                    echo template(EnumViewFile::ModuleItem);
                }
                ?>
            </div>
            <div>
                <?php
                echo template(EnumViewFile::ModulePaginator);
                ?>
            </div>
        </div>
    </div>
</div>