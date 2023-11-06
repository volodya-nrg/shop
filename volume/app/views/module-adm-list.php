<div class="module-adm-list">
    <div class="module-adm-list_top">
        <form class="module-adm-list_search" method="get" action="">
            <input type="text" name="filter" placeholder=""/>
        </form>

        <a href="/adm/items/item/0">+ Добавить</a>
    </div>
</div>
<br/>
<div>
    <?php foreach ([1, 2, 3] as $key => $val): ?>
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