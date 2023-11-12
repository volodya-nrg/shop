<?php declare(strict_types=1);
$err = $__err ?? "";
$styles = $__data[EnumField::Styles->value] ?? "";
$item = $__data[EnumField::Item->value] ?? new ItemRow([]);
?>
<form class="module-counter<?php if ($styles != "") echo " {$styles}"; ?>" onsubmit="return false;">
    <button class="module-counter_plus">-</button>
    <input class="module-counter_amount align-center" type="number" value="0">
    <button class="module-counter_minus">+</button>
</form>