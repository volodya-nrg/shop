<?php
$error = $__data[FieldError] ?? "";
$successMsg = $__data[FieldSuccess] ?? "";
$email = $__data[FieldEmail] ?? "";
?>
<div class="block_center_and_slim">
    <h1 class="align-center">Смена пароля</h1>

    <?php if ($error != ""): ?>
        <div>
            <?php
            echo template(DIR_VIEWS . "/" . ViewModuleNotice, [
                FieldMsg => $error,
                FieldType => NoticeStyleClassDanger,
            ]);
            ?>
        </div>
        <br/>
    <?php elseif ($successMsg != ""): ?>
        <div>
            <?php
            echo template(DIR_VIEWS . "/" . ViewModuleNotice, [
                FieldMsg => $successMsg,
                FieldType => NoticeStyleClassSuccess,
            ]);
            ?>
        </div>
        <br/>
    <?php endif; ?>

    <?php if ($email != ""): ?>
        <form method="post" class="form" action="">
            <div class="form__row">
                <div class="form__title">Пароль</div>
                <input type="password" name="<?php echo FieldPassword ?>" value="" required="required"/>
            </div>
            <div class="form__row">
                <div class="form__title">Пароль (павтор)</div>
                <input type="password" name="<?php echo FieldPasswordConfirm ?>" value="" required="required"/>
            </div>
            <div class="form__row align-right">
                <input class="btn" type="submit" value="Отправить"/>
            </div>
        </form>
    <?php endif; ?>
</div>