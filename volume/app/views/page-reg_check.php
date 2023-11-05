<?php
$err = $__data[EnumField::Error->value] ?? "";
$msg = $__data[EnumField::Msg->value] ?? "";
?>
<div class="block_center_and_slim">
    <?php echo $err ?>
    <?php echo $msg ?>
</div>