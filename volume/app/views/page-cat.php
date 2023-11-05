<?php
$error = $__data[EnumField::Error->value] ?? "";
?>
<div class="page-cat">
    <div class="page-cat__menu">
        <?php
        echo template(EnumViewFile::ModuleCatalogMenu);
        ?>
    </div>
    <div class="page-cat__main">
        <div class="page-cat__breakcrumbs">
            <?php
            echo template(EnumViewFile::ModuleBreakCrumbs);
            ?>
        </div>
        <div class="page-cat__items">
            <?php
            for ($i = 0; $i < 3; $i++) {
                echo template(EnumViewFile::ModuleItem);
            }
            ?>
        </div>
        <div class="page-cat__paginator">
            <?php
            echo template(EnumViewFile::ModulePaginator);
            ?>
        </div>
    </div>
</div>