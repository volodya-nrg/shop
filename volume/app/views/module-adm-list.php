<?php declare(strict_types=1);
$err = $__err ?? "";
$path = $__data[EnumField::Path->value] ?? "";
$items = $__data[EnumField::Items->value] ?? [];
$offset = $__data[EnumField::Offset->value] ?? 0;
$filter = $__data[EnumField::Filter->value] ?? "";
?>
<div class="module-adm-list">
    <div class="module-adm-list_top">
        <form class="module-adm-list_search">
            <input type="text"
                   name="<?php echo EnumField::Filter->value ?>"
                   value="<?php echo $filter ?>"
                   placeholder="<?php echo EnumDic::Filter->value ?>"/>
        </form>

        <a href="<?php echo sprintf("%s/%d", $path, 0) ?>"><?php echo EnumDic::AddWithPlus->value ?></a>
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