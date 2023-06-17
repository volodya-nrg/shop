<?php
$item = $__data[FieldItem] ?? new Item();
?>
<div class="page-item">
    <div class="page-item__breadcrumbs">
        <?php
        echo template(DIR_TEMPLATES . "/" . ViewModuleBreakCrumbs, []);
        ?>
    </div>
    <div class="page-item__main">
        <div class="page-item__cover">
            <?php if (count($item->pics)): ?>
                <?php foreach ($item->pics as $val): ?>
                    <img src="<?php echo $val ?>">
                <?php endforeach; ?>
            <?php else: ?>
                <img src="/images/default-item.png">
            <?php endif; ?>
        </div>
        <div class="page-item__data">
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
                <?php echo template(DIR_TEMPLATES . "/" . ViewModuleCounter, [
                    FieldStyles => "sx-lg",
                    FieldItem => $item,
                ]) ?>
            </div>
        </div>
    </div>
    <div class="page-item__info">
        <p><?php echo $item->description ?></p>
    </div>
</div>
