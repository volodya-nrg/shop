<?php declare(strict_types=1);
$err = $__err ?? "";
$dataSendMsg = $__data[EnumField::DataSendMsg->value] ?? "";
?>
<div class="main">
    <div class="main_column">
        <h1 class="align-center">Восстановление доступа</h1>

        <?php if ($err): ?>
            <div>
                <?php
                echo template(EnumViewFile::ModuleNotice, "", [
                    EnumField::Msg->value => $err,
                    EnumField::Type->value => EnumNoticeStyleClass::Danger->value,
                ]);
                ?>
            </div>
            <br/>
        <?php elseif ($dataSendMsg): ?>
            <div>
                <?php
                echo template(EnumViewFile::ModuleNotice, "", [
                    EnumField::Msg->value => $dataSendMsg,
                    EnumField::Type->value => EnumNoticeStyleClass::Success->value,
                ]);
                ?>
            </div>
            <br/>
        <?php endif; ?>

        <form method="post" class="form" action="">
            <div class="form_row">
                <div class="form_title">Е-мэйл</div>
                <input type="email" name="<?php echo EnumField::Email->value ?>" value="" required="required"/>
            </div>
            <div class="form_row align-right">
                <input class="btn" type="submit" value="Отправить"/>
            </div>
        </form>
    </div>
</div>