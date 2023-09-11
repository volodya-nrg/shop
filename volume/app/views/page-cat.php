<?php
$error = $__data[FieldError] ?? "";
?>
<div class="page-cat">
    <div class="page-cat__menu">
        <?php
        echo template(DIR_VIEWS . "/" . ViewModuleCatalogMenu, []);
        ?>
    </div>
    <div class="page-cat__main">
        <div class="page-cat__breakcrumbs">
            <?php
            echo template(DIR_VIEWS . "/" . ViewModuleBreakCrumbs, []);
            ?>
        </div>
        <div class="page-cat__items">
            <?php
            for ($i = 0; $i < 3; $i++) {
                echo template(DIR_VIEWS . "/" . ViewModuleItem, []);
            }
            ?>
        </div>
        <div class="page-cat__paginator">
            <?php
            echo template(DIR_VIEWS . "/" . ViewModulePaginator, []);
            ?>
        </div>
    </div>
</div>