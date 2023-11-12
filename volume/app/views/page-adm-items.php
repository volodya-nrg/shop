<?php declare(strict_types=1);
$err = $__err ?? "";
$tabs = $__data[EnumField::Tabs->value] ?? [];
$items = $__data[EnumField::Items->value] ?? [];
?>
<div class="main">
    <div class="main_column">
        <?php
        $items = template(EnumViewFile::ModuleAdmList, $items);
        $paginator = template(EnumViewFile::ModulePaginator, "", [
            EnumField::Path->value => "/adm/items",
            EnumField::From->value => 0,
        ]);
        echo template(EnumViewFile::ModuleTabs, "", [
            EnumField::Tabs->value => $tabs,
            EnumField::Content->value => "{$items}<br/>{$paginator}",
        ]);
        ?>
    </div>
</div>