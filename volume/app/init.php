<?php

// for database
define("DB_HOST", getenv("DB_HOST") ?: "");
define("DB_NAME", getenv("DB_NAME") ?: "");
define("DB_USER", getenv("DB_USER") ?: "");
define("DB_PASS", getenv("DB_PASS") ?: "");
define("DB_CHARSET", getenv("DB_CHARSET") ?: "");

// paths
define("DIR_APP", dirname(__FILE__));
const DIR_PUBLIC_HTML = DIR_APP . "/../public_html";
const DIR_TEMPLATES = DIR_APP . "/templates";
const DIR_CONTROLLERS = DIR_APP . "/controllers";
const DIR_CLASSES = DIR_APP . "/classes";

// other
const PassMinLen = 5;

// notice
const NoticeStyleClassDanger = "sx-danger";
const NoticeStyleClassWarning = "sx-warning";
const NoticeStyleClassInfo = "sx-info";
const NoticeStyleClassSuccess = "sx-success";

// request, response, params fields
const FieldEmail = "email";
const FieldPassword = "password";
const FieldPasswordConfirm = "password_confirm";
const FieldAgreement = "agreement";
const FieldPrivacyPolicy = "privacy_policy";
const FieldErrors = "errors";
const FieldRequestedEmail = "requested_email";
const FieldRequestedAgreement = "requested_agreement";
const FieldRequestedPrivatePolicy = "requested_private_policy";
const FieldMsg = "msg";
const FieldType = "type";
const FieldDataSendMsg = "data_is_send";
const FieldSuccess = "success";
const FieldHash = "hash";
const FieldStyles = "styles";
const FieldItem = "item";
const FieldPhoneNumber = "phone_number";
const FieldFIO = "fio";
const FieldDeliveryTo = "delivery_to";

// dictionary
const DicEnter = "Вход";
const DicRegistration = "Регистрация";
const DicPageNotFound = "Страница не найдена";
const DicAdministration = "Администрирование";
const DicPageMain = "Главная страница";
const DicAgreement = "Соглашение";
const DicRecoverAccess = "Восстановление доступа";
const DicRecoverDataSendMsgTpl = "Данные отправлены на ваш е-мэйл (%s). Следуйте инструкциям в письме.";
const DicChangePassword = "Смена пароля";
const DicPasswordChangedSuccessfully = "пароль успешно изменен";
const DicCatalog = "Каталог";
const DicOrder = "Заказ";

// errors
const ErrNotConnectToDatabase = "нет соединения с БД";
const ErrNotFoundClass = "не найден class";
const ErrNotFoundMethod = "не найден method";
const ErrNotFoundFileTpl = "не найден файл: %s";
const ErrPassIsShort = "пароль слишком короткий (минимум " . PassMinLen . " символов)";
const ErrPasswordsNotEqual = "пароли не совподают";
const ErrEmailNotCorrect = "е-мэйл не корректный";
const ErrAcceptAgreement = "примите условия оферты";
const ErrAcceptPrivatePolicy = "примите политику конфиденциальности";
const ErrMethodNotAllowed = "метод не разрешен";

// viewNames
const ViewModuleCounter = "module-counter.php";
const ViewModuleItem = "module-item.php";
const ViewModuleCartItem = "module-cart-item.php";
const ViewModuleNotice = "module-notice.php";
const ViewModuleBreakCrumbs = "module-breakcrumbs.php";
const ViewModuleCatalogMenu = "module-catalog-menu.php";
const ViewModulePaginator = "module-paginator.php";
const ViewPageAdm = "page-adm.php";
const ViewPageAgreement = "page-agreement.php";
const ViewPageCart = "page-cart.php";
const ViewPageCat = "page-cat.php";
const ViewPageCheckout = "page-checkout.php";
const ViewPageContacts = "page-contacts.php";
const ViewPageInfo = "page-info.php";
const ViewPageItem = "page-item.php";
const ViewPageLogin = "page-login.php";
const ViewPageMain = "page-main.php";
const ViewPageNotFound = "page-notfound.php";
const ViewPagePrivacyPolicy = "page-privacy-policy.php";
const ViewPageProfile = "page-profile.php";
const ViewPageRecover = "page-recover.php";
const ViewPageRecoverChecker = "page-recover-checker.php";
const ViewPageReg = "page-reg.php";
const ViewPageSearch = "page-search.php";
const ViewPageOrder = "page-order.php";
const ViewPageOrderOk = "page-order-ok.php";

// functions
function template(string $__view, array $__data = [])
{
    $output = "";

    if (ob_start()) {
        //extract($__data);
        require($__view);
        $output = ob_get_contents();
        ob_end_clean(); // очистить, но не вывести в браузер
    }

    return $output;
}

function redirect($url)
{
    header("Location: {$url}");
    exit;
}

function randomString(int $length = 20): string
{
    $characters = "abcdefghijklmnopqrstuvwxyz";
    $charactersLength = strlen($characters);
    $randomString = "";
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function randomEmail(): string
{
    return sprintf("%s@%s.%s", randomString(10), randomString(5), randomString(3));
}

function myAutoload($className): void
{
    $aParts = [];
    $file = "{$className}.php";

    if (substr($className, 0, mb_strlen("Controller")) === "Controller") {
        $aParts[] = DIR_CONTROLLERS;
    } else {
        if (file_exists(DIR_CLASSES . "/" . $file)) {
            $aParts[] = DIR_CLASSES;
        }
    }

    $aParts[] = $file;
    $filepath = implode("/", $aParts);

    if (file_exists($filepath)) {
        require_once $filepath;
    }
}

function finePrice(int $price): string {
    return sprintf("%d", $price);
}

// ---------------------------------------------------------------------------------------------------------------------
spl_autoload_register('myAutoload');