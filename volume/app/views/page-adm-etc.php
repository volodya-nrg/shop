<?php declare(strict_types=1);
$tabs = $__data[EnumField::Tabs->value] ?? [];
?>
<div class="main">
    <div class="main_column">
        <?php

        echo template(EnumViewFile::ModuleTabs, [
            EnumField::Tabs->value => $tabs,
            EnumField::Content->value => "",
        ]);
        ?>

        <a href="?addData=1">add data</a>
    </div>
</div>