<?php
$error = $__data[EnumField::Error->value] ?? "";
$successMsg = $__data[EnumField::Success->value] ?? "";
$email = $__data[EnumField::Email->value] ?? "";
?>
<div class="block_center_and_slim">
    <h1 class="align-center">Смена пароля</h1>

    <?php if ($error != ""): ?>
        <div>
            <?php
            echo template(EnumViewFile::ModuleNotice, [
                EnumField::Msg->value => $error,
                EnumField::Type->value => EnumNoticeStyleClass::Danger->value,
            ]);
            ?>
        </div>
        <br/>
    <?php elseif ($successMsg != ""): ?>
        <div>
            <?php
            echo template(EnumViewFile::ModuleNotice, [
                EnumField::Msg->value => $successMsg,
                EnumField::Type->value => EnumNoticeStyleClass::Success->value,
            ]);
            ?>
        </div>
        <br/>
    <?php endif; ?>

    <?php if ($email != ""): ?>
        <form method="post" class="form" action="">
            <div class="form__row">
                <div class="form__title">Пароль</div>
                <input type="password" name="<?php echo EnumField::Password->value ?>" value="" required="required"/>
            </div>
            <div class="form__row">
                <div class="form__title">Пароль (павтор)</div>
                <input type="password" name="<?php echo EnumField::PasswordConfirm->value ?>" value="" required="required"/>
            </div>
            <div class="form__row align-right">
                <input class="btn" type="submit" value="Отправить"/>
            </div>
        </form>
    <?php endif; ?>
</div>