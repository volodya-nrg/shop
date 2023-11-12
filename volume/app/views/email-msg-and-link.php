<?php declare(strict_types=1);
$err = $__err ?? "";
$msg = $__data[EnumField::Address->value] ?? "";
$address = $__data[EnumField::Address->value] ?? "";
?>
<p>
    <?php echo $msg ?> <a href="<?php echo $address ?>"><?php echo $address ?></a>
</p>
