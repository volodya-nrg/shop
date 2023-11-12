<?php declare(strict_types=1);
$err = $__err ?? "";
?>
<div class="module-item float-left">
    <a class="module-item_cover" href="/item/1">
        <img src="/images/internal/default-item.png" alt="Lorem ipsum dolor"/>
    </a>
    <div class="module-item_content">
        <a class="module-item_title" href="/item/1">Lorem ipsum dolor</a>
        <div class="module-item_price-and-counter">
            <div class="module-item_price">
                0 <span class="text-small">₽</span>
            </div>
            <div class="module-item_counter">
                <?php echo template(EnumViewFile::ModuleCounter) ?>
            </div>
        </div>
    </div>
</div>
