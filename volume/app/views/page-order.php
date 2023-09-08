<?php
$error = $__data[FieldError] ?? "";
$requestedEmail = $__data[FieldEmail] ?? "";
$requestedPhoneNumber = $__data[FieldPhoneNumber] ?? "";
$requestedFIO = $__data[FieldFIO] ?? "";
$requestedDeliveryTo = $__data[FieldDeliveryTo] ?? "";
?>
<div class="page-order">
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
    <?php endif; ?>
    <form method="post" class="form" action="">
        <div class="form__row">
            <div class="form__title">Е-мэйл *</div>
            <input type="email" name="<?php echo FieldEmail ?>" value="<?php echo $requestedEmail ?>"
                   required="required"/>
        </div>
        <div class="form__row">
            <div class="form__title">Номер телефона *</div>
            <input type="password" name="<?php echo FieldPhoneNumber ?>" value="<?php echo $requestedPhoneNumber ?>"
                   required="required"/>
        </div>
        <div class="form__row">
            <div class="form__title">ФИО</div>
            <input type="text" name="<?php echo FieldFIO ?>" value="<?php echo $requestedFIO ?>"/>
        </div>
        <div class="form__row">
            <div class="form__title">Доставить до</div>
            <input type="text" name="<?php echo FieldDeliveryTo ?>" value="<?php echo $requestedDeliveryTo ?>"/>
        </div>
        <div class="form__row">
            <input class="btn" type="submit" value="Отправить"/>
        </div>
    </form>
</div>