<?php declare(strict_types=1);
$err = $__err ?? "";
$tabs = $__data[EnumField::Tabs->value] ?? [];
$content = $__data[EnumField::Content->value] ?? "";
?>
<div class="tabs">
    <div class="tabs_host-items">
        <?php foreach ($tabs as $tab): ?>
            <a class="tabs_host-item<?php echo $tab->isActive ? " sx-active" : ""; ?>"
               href="<?php echo $tab->link ?>">
                <?php echo $tab->name ?>
            </a>
        <?php endforeach; ?>
    </div>
    <div class="tabs_contents">
        <?php echo $content ?>
    </div>
</div>