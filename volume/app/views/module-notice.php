<?php
$msg = $__data[EnumField::Msg->value] ?? "";
$type = $__data[EnumField::Type->value] ?? "";
?>
<div class="notice <?php echo $type ?>">
    <?php echo $msg ?>
</div>
