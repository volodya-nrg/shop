<?php
$item = $__data[EnumField::Item->value] ?? new ItemRow([]);
?>
<div class="page-item">
    <div class="page-item_breadcrumbs">
        <?php
        echo template(EnumViewFile::ModuleBreakCrumbs);
        ?>
    </div>
    <div class="page-item_main">
        <div class="page-item_cover">
            <?php if (count($item->pics)): ?>
                <?php foreach ($item->pics as $val): ?>
                    <img src="<?php echo $val ?>">
                <?php endforeach; ?>
            <?php else: ?>
                <img src="/images/internal/default-item.png">
            <?php endif; ?>
        </div>
        <div class="page-item_data">
            <h1><?php echo $item->title ?></h1>
            <?php if (count($item->props)): ?>
                <ul>
                    <?php foreach ($item->props as $val): ?>
                        <li><?php echo $val ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <div class="text-nowrap">
                Цена: <span class="h3"><?php echo finePrice($item->price) ?></span> ₽
                <?php echo template(EnumViewFile::ModuleCounter, [
                    EnumField::Styles->value => "sx-lg",
                    EnumField::Item->value => $item,
                ]) ?>
            </div>
        </div>
    </div>
    <div class="page-item_info">
        <p><?php echo $item->description ?></p>
    </div>
</div>
