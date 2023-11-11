<?php
declare(strict_types=1);

$tabs = $__data[EnumField::Tabs->value] ?? [];
$items = $__data[EnumField::Items->value] ?? [];
?>
<div class="module-adm-list">
    <div class="module-adm-list_top">
        <form class="module-adm-list_search" method="get" action="">
            <input type="text" name="filter" placeholder="filter"/>
            <input type="submit" value="Ok"/>
        </form>

        <a href="/adm/items/item/0">+ Добавить</a>
    </div>
</div>
<br/>
<div>
    <?php foreach ($items as $key => $val): ?>
        <div class="module-adm-list_item">
            <div class="module-adm-list_item_pos text-mute">
                <?php echo $key ?>.
            </div>
            <div class="module-adm-list_item_name text-eclipse">
                <?php echo randomString() ?>
            </div>
            <div class="module-adm-list_item_edit">
                <a href="<?php echo "/adm/items/item/{$val}" ?>"><img src="/images/internal/pen-solid.svg"/></a>
            </div>
        </div>
    <?php endforeach; ?>
</div>