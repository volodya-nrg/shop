<?php
$errors = $__data[FieldErrors] ?? [];
$successMsg = $__data[FieldSuccess] ?? "";
$email = $__data[FieldEmail] ?? "";
?>
<div class="block_center_and_slim">
    <h1 class="align-center">Смена пароля</h1>

    <?php if (count($errors)): ?>
        <div>
            <?php
            foreach ($errors as $msg) {
                echo template(DIR_TEMPLATES . "/" . ViewModuleNotice, [
                    FieldMsg => $msg,
                    FieldType => NoticeStyleClassDanger,
                ]);
            }
            ?>
        </div>
        <br/>
    <?php elseif ($successMsg != ""): ?>
        <div>
            <?php
            echo template(DIR_TEMPLATES . "/" . ViewModuleNotice, [
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