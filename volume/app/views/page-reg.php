<?php
$error = $__data[EnumField::Error->value] ?? "";
$requestedEmail = $__data[EnumField::RequestedEmail->value] ?? "";
$requestedAgreement = $__data[EnumField::RequestedAgreement->value] ?? false;
$requestedPrivatePolicy = $__data[EnumField::RequestedPrivatePolicy->value] ?? false;
?>
<div class="main">
    <div class="main_column">
        <h1 class="align-center">Регистрация</h1>

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
        <?php endif; ?>

        <form method="post" class="form" action="">
            <div class="form_row">
                <div class="form_title">Е-мэйл</div>
                <input type="email" name="<?php echo EnumField::Email->value ?>" value="<?php echo $requestedEmail ?>"
                       required="required"/>
            </div>
            <div class="form_row">
                <div class="form_title">Пароль</div>
                <input type="password" name="<?php echo EnumField::Password->value ?>" value="" required="required"/>
            </div>
            <div class="form_row">
                <div class="form_title">Пароль (павтор)</div>
                <input type="password" name="<?php echo EnumField::PasswordConfirm->value ?>" value="" required="required"/>
            </div>
            <div class="form_row">
                <label for="page-reg-checkbox-agreement">
                    <input id="page-reg-checkbox-agreement"
                           name="<?php echo EnumField::Agreement->value ?>"
                           <?php if ($requestedAgreement): ?>checked="checked"<?php endif; ?>
                           type="checkbox"
                           required="required"
                    /> Я принимаю <a href="/agreement">условия оферты</a>
                </label>
            </div>
            <div class="form_row">
                <label for="page-reg-checkbox-privacy-policy">
                    <input id="page-reg-checkbox-privacy-policy"
                           name="<?php echo EnumField::PrivacyPolicy->value ?>"
                           <?php if ($requestedPrivatePolicy): ?>checked="checked"<?php endif; ?>
                           type="checkbox"
                           required="required"
                    /> Я принимаю <a href="/privacy-policy">политику конфиденциальности</a>
                </label>
            </div>
            <div class="form_row align-right">
                <input class="btn" type="submit" value="Отправить"/>
            </div>
        </form>
    </div>
</div>