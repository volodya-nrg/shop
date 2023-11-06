<?php
$path = $__data[EnumField::Path->value] ?? "";
$from = $__data[EnumField::From->value] ?? 0;
$to = 10 + $from;
?>
<div class="module-paginator">
    <?php for ($i = $from; $i < $to; $i++): ?>
        <a class="module-paginator_item"
           href="<?php echo sprintf("%s?%s=%d", $path, EnumField::Page->value, $i) ?>"><?php echo $i ?></a>
    <?php endfor; ?>
</div>