<?php declare(strict_types=1);
$err = $__err ?? "";
$msg = $__data[EnumField::Msg->value] ?? "";
?>
<div class="main">
    <div class="main_column">
        <div>
            bread
        </div>
        <div>
            <h1>Lorem ipsum dolor sit amet, consectetur adipisicing elit</h1>
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ad adipisci aut autem est iste maiores ntem
                voluptates.</p>
            <?php echo $err ?>
            <?php echo $msg ?>
        </div>
    </div>
</div>