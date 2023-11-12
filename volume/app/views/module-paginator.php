<?php declare(strict_types=1);
$err = $__err ?? "";
$total = $__data[EnumField::Total->value] ?? 0;
$offset = $__data[EnumField::Offset->value] ?? 0;
$filter = $__data[EnumField::Filter->value] ?? "";
$curPage = (int)floor($offset / DefaultLimit);
$totalPages = (int)ceil($total / DefaultLimit);

$filterLoc = "";
if ($filter !== "") {
    $filterLoc = sprintf("&%s=%s", EnumField::Filter->value, $filter);
}
?>
<div class="module-paginator">
    <?php for ($i = 0, $j = 1; $i < $totalPages; $i++, $j++): ?>
        <?php if ($curPage === $i): ?>
            <a class="module-paginator_item"><?php echo $j ?></a>
        <?php else: ?>
            <a class="module-paginator_item"
               href="?<?php echo sprintf("%s=%d", EnumField::Page->value, $j) . $filterLoc ?>"><?php echo $j ?></a>
        <?php endif; ?>
    <?php endfor; ?>
</div>