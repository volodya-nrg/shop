<?php
$errors = $__data[FieldErrors] ?? [];
?>
<div class="page-cat">
    <div class="page-cat__menu">
        <?php
        echo template(DIR_TEMPLATES . "/" . ViewModuleCatalogMenu, []);
        ?>
    </div>
    <div class="page-cat__main">
        <div class="page-cat__breakcrumbs">
            <?php
            echo template(DIR_TEMPLATES . "/" . ViewModuleBreakCrumbs, []);
            ?>
        </div>
        <div class="page-cat__items">
            <?php
            for ($i = 0; $i < 3; $i++) {
                echo template(DIR_TEMPLATES . "/" . ViewModuleItem, []);
            }
            ?>
        </div>
        <div class="page-cat__paginator">
            <?php
            echo template(DIR_TEMPLATES . "/" . ViewModulePaginator, []);
            ?>
        </div>
    </div>
</div>