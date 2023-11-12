<?php declare(strict_types=1);
$err = $__err ?? "";
$type = $__data[EnumField::Type->value] ?? "";
$msg = $__data[EnumField::Msg->value] ?? "";
?>
<div class="notice <?php echo $type ?>">
    <?php echo $msg ?>
</div>
