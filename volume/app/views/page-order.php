<?php
$error = $__data[EnumField::Error->value] ?? "";
$requestedEmail = $__data[EnumField::Email->value] ?? "";
$requestedPhoneNumber = $__data[EnumField::PhoneNumber->value] ?? "";
$requestedFIO = $__data[EnumField::FIO->value] ?? "";
$requestedDeliveryTo = $__data[EnumField::DeliveryTo->value] ?? "";
?>
<div class="page-order">
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
            <div class="form_title">Е-мэйл *</div>
            <input type="email" name="<?php echo EnumField::Email->value ?>" value="<?php echo $requestedEmail ?>"
                   required="required"/>
        </div>
        <div class="form_row">
            <div class="form_title">Номер телефона *</div>
            <input type="password" name="<?php echo EnumField::PhoneNumber->value ?>" value="<?php echo $requestedPhoneNumber ?>"
                   required="required"/>
        </div>
        <div class="form_row">
            <div class="form_title">ФИО</div>
            <input type="text" name="<?php echo EnumField::FIO->value ?>" value="<?php echo $requestedFIO ?>"/>
        </div>
        <div class="form_row">
            <div class="form_title">Доставить до</div>
            <input type="text" name="<?php echo EnumField::DeliveryTo->value ?>" value="<?php echo $requestedDeliveryTo ?>"/>
        </div>
        <div class="form_row">
            <input class="btn" type="submit" value="Отправить"/>
        </div>
    </form>
</div>