<?php
$error = $__data[EnumField::Error->value] ?? "";
?>
<div class="page-cat">
    <div class="page-cat_menu">
        <?php
        echo template(EnumViewFile::ModuleCatalogMenu);
        ?>
    </div>
    <div class="page-cat_main">
        <div class="page-cat_breakcrumbs">
            <?php
            echo template(EnumViewFile::ModuleBreakCrumbs);
            ?>
        </div>
        <div class="page-cat_items">
            <?php
            for ($i = 0; $i < 3; $i++) {
                echo template(EnumViewFile::ModuleItem);
            }
            ?>
        </div>
        <div class="page-cat_paginator">
            <?php
            echo template(EnumViewFile::ModulePaginator);
            ?>
        </div>
    </div>
</div>