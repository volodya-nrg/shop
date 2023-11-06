<?php

enum EnumDic: string
{
    case Administration = "Администрирование";
    case Agreement = "Соглашение";
    case Catalog = "Каталог";
    case ChangePassword = "Смена пароля";
    case EmailSuccessfullyConfirmed = "Е-мэйл успешно подтвержден";
    case Enter = "Вход";
    case GoAheadForRecoverPass = "Пройдите по ссылке, для восстановления пароля:";
    case GoAheadForVerifyEmail = "Пройдите по ссылке, для верификации е-мэйла:";
    case Order = "Заказ";
    case PageMain = "Главная страница";
    case PageNotFound = "Страница не найдена";
    case PasswordChangedSuccessfully = "Пароль успешно изменен";
    case RecoverAccess = "Восстановление доступа";
    case RecoverDataSendMsgTpl = "Данные отправлены на ваш е-мэйл (%s). Следуйте инструкциям в письме.";
    case Registration = "Регистрация";
    case VerifyEmail = "Верификация е-мэйла";
}