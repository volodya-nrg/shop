<?php
$error = $__data[FieldError] ?? "";
$requestedEmail = $__data[FieldRequestedEmail] ?? "";
?>
<div class="block_center_and_slim">
    <h1 class="align-center">Вход</h1>

    <?php if ($error !== ""): ?>
        <div>
            <?php
            echo template(DIR_VIEWS . "/" . ViewModuleNotice, [
                FieldMsg => $error,
                FieldType => NoticeStyleClassDanger,
            ]);
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
        <div class="form__row align-right">
            <a href="/recover">Забыли пароль?</a>
        </div>
        <div class="form__row">
            <input class="btn" type="submit" value="Отправить"/>
        </div>
        <div class="form__row">
            <hr class="hr"/>
        </div>
        <div class="form__row align-center">
            <a href="/reg">Зарегистрироваться</a>
        </div>
    </form>
</div>