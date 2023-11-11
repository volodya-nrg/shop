<?php declare(strict_types=1);
$path = $__data[EnumField::Path->value] ?? "";
$items = $__data[EnumField::Items->value] ?? [];
$offset = $__data[EnumField::Offset->value] ?? 0;
?>
<div class="module-adm-list">
    <div class="module-adm-list_top">
        <form class="module-adm-list_search" method="get" action="">
            <input type="text" name="<?php echo $_GET[EnumField::Filter->value] ?? "" ?>" placeholder="filter"/>
        </form>

        <a href="<?php echo sprintf("%s/%d", $path, 0) ?>">+ Добавить</a>
    </div>
</div>
<br/>
<div>
    <?php foreach ($items as $key => $item): ?>
        <div class="module-adm-list_item">
            <div class="module-adm-list_item_pos text-mute">
                <?php echo $item->pos + $offset ?>.
            </div>
            <div class="module-adm-list_item_name text-eclipse">
                <?php echo $item->title ?>
            </div>
            <div class="module-adm-list_item_edit">
                <a href="<?php echo sprintf("%s/%d", $path, $item->id) ?>"><img
                            src="/images/internal/pen-solid.svg"/></a>
            </div>
        </div>
    <?php endforeach; ?>
</div>