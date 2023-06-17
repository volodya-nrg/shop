<?php
$errors = $__data[FieldErrors] ?? [];
$requestedEmail = $__data[FieldRequestedEmail] ?? "";
$requestedAgreement = $__data[FieldRequestedAgreement] ?? false;
$requestedPrivatePolicy = $__data[FieldRequestedPrivatePolicy] ?? false;
?>
<div class="block_center_and_slim">
    <h1 class="align-center">Регистрация</h1>

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
    <?php endif; ?>

    <form method="post" class="form" action="">
        <div class="form__row">
            <div class="form__title">Е-мэйл</div>
            <input type="email" name="<?php echo FieldEmail ?>" value="<?php echo $requestedEmail ?>"
                   required="required"/>
        </div>
        <div class="form__row">
            <div class="form__title">Пароль</div>
            <input type="password" name="<?php echo FieldPassword ?>" value="" required="required"/>
        </div>
        <div class="form__row">
            <div class="form__title">Пароль (павтор)</div>
            <input type="password" name="<?php echo FieldPasswordConfirm ?>" value="" required="required"/>
        </div>
        <div class="form__row">
            <label for="page-reg-checkbox-agreement">
                <input id="page-reg-checkbox-agreement"
                       name="<?php echo FieldAgreement ?>"
                       <?php if ($requestedAgreement): ?>checked="checked"<?php endif; ?>
                       type="checkbox"
                       required="required"
                /> Я принимаю <a href="/agreement">условия оферты</a>
            </label>
        </div>
        <div class="form__row">
            <label for="page-reg-checkbox-privacy-policy">
                <input id="page-reg-checkbox-privacy-policy"
                       name="<?php echo FieldPrivacyPolicy ?>"
                       <?php if ($requestedPrivatePolicy): ?>checked="checked"<?php endif; ?>
                       type="checkbox"
                       required="required"
                /> Я принимаю <a href="/privacy-policy">политику конфиденциальности</a>
            </label>
        </div>
        <div class="form__row align-right">
            <input class="btn" type="submit" value="Отправить"/>
        </div>
    </form>
</div>