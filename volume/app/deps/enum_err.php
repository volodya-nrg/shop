<?php
declare(strict_types=1);

enum EnumErr: string
{
    case NotHasAccess = "нет доступа";
    case NotConnectToDatabase = "нет соединения с БД";
    case NotFoundClass = "не найден class";
    case NotFoundMethod = "не найден method";
    case NotFoundRow = "не найдена запись";
    case PassIsShortTpl = "пароль слишком короткий (минимум %d символов)";
    case PasswordsNotEqual = "пароли не совподают";
    case EmailNotCorrect = "е-мэйл не корректный";
    case AcceptAgreement = "примите условия оферты";
    case AcceptPrivatePolicy = "примите политику конфиденциальности";
    case MethodNotAllowed = "метод не разрешен";
    case UserAlreadyHas = "такой пользователь уже существует, зайдите под своей учетной записи";
    case CheckYourEmail = "подтвердите свой е-мэйл";
    case InternalServer = "внутреняя ошибка сервера";
    case InWhenTpl = "error in %s when '%s' -> %s";
    case LoginOrPasswordNotCorrect = "логин или пароль не корректны";
    case StmtIsFalse = "stmt is false";
    case PrepareIsFalse = "prepare is false";
    case SqlQueryIsFalse = "sql query is false";
}