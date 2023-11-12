<?php declare(strict_types=1);
$err = $__err ?? "";
$tabs = $__data[EnumField::Tabs->value] ?? [];
$items = $__data[EnumField::Items->value] ?? [];
$total = $__data[EnumField::Total->value] ?? 0;
$offset = $__data[EnumField::Offset->value] ?? 0;
$filter = $__data[EnumField::Filter->value] ?? "";
?>
<div class="main">
    <div class="main_column">
        <?php
        $items = template(EnumViewFile::ModuleAdmList, "", [
            EnumField::Path->value => "/adm/cat",
            EnumField::Items->value => $items,
            EnumField::Offset->value => $offset,
            EnumField::Filter->value => $filter,
        ]);
        $paginator = template(EnumViewFile::ModulePaginator, "", [
            EnumField::Total->value => $total,
            EnumField::Offset->value => $offset,
            EnumField::Filter->value => $filter,
        ]);
        echo template(EnumViewFile::ModuleTabs, "", [
            EnumField::Tabs->value => $tabs,
            EnumField::Content->value => "{$items}<br/>{$paginator}",
        ]);
        ?>
    </div>
</div>