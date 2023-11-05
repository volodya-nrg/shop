<?php
$styles = $__data[EnumField::Styles->value] ?? "";
$item = $__data[EnumField::Item->value] ?? new ItemRow([]);
?>
<form class="module-counter<?php if ($styles != "") echo " {$styles}"; ?>" onsubmit="return false;">
    <button class="module-counter__plus">-</button>
    <input class="module-counter__amount align-center" type="number" value="0">
    <button class="module-counter__minus">+</button>
</form>