<?php
$error = $__data[FieldError] ?? "";
$dataSendMsg = $__data[FieldDataSendMsg] ?? "";
?>
<div class="block_center_and_slim">
    <h1 class="align-center">Восстановление доступа</h1>

    <?php if ($error != ""): ?>
        <div>
            <?php
            echo template(DIR_TEMPLATES . "/" . ViewModuleNotice, [
                FieldMsg => $error,
                FieldType => NoticeStyleClassDanger,
            ]);
            ?>
        </div>
        <br/>
    <?php elseif ($dataSendMsg != ""): ?>
        <div>
            <?php
            echo template(DIR_TEMPLATES . "/" . ViewModuleNotice, [
                FieldMsg => $dataSendMsg,
                FieldType => NoticeStyleClassSuccess,
            ]);
            ?>
        </div>
        <br/>
    <?php endif; ?>

    <form method="post" class="form" action="">
        <div class="form__row">
            <div class="form__title">Е-мэйл</div>
            <input type="email" name="<?php echo FieldEmail ?>" value="" required="required"/>
        </div>
        <div class="form__row align-right">
            <input class="btn" type="submit" value="Отправить"/>
        </div>
    </form>
</div>