<?php
define("ADDRESS", getenv("ADDRESS") ?: "http://localhost");

define("DB_HOST", getenv("DB_HOST") ?: "");
define("DB_NAME", getenv("DB_NAME") ?: "");
define("DB_USER", getenv("DB_USER") ?: "");
define("DB_PASS", getenv("DB_PASS") ?: "");
define("DB_CHARSET", getenv("DB_CHARSET") ?: "");

define("EMAIL_SMTP_SERVER", getenv("EMAIL_SMTP_SERVER") ?: "");
define("EMAIL_PORT", getenv("EMAIL_PORT") ?: "");
define("EMAIL_LOGIN", getenv("EMAIL_LOGIN") ?: "");
define("EMAIL_PASS", getenv("EMAIL_PASS") ?: "");
define("EMAIL_FROM", getenv("EMAIL_FROM") ?: "");

// paths
define("DIR_APP", dirname(__FILE__));
const DIR_PUBLIC_HTML = DIR_APP . "/../public_html";
const DIR_VIEWS = DIR_APP . "/views";
const DIR_CONTROLLERS = DIR_APP . "/controllers";
const DIR_CLASSES = DIR_APP . "/classes";
const DIR_REQUESTS = DIR_APP . "/requests";
const DIR_SERVICES = DIR_APP . "/services";
const DIR_INTERFACES = DIR_APP . "/interfaces";

// other
const PassMinLen = 5;
const DatePattern = "Y-m-d H:i:s";
const DefaultLimit = 20;

// notice
enum EnumNoticeStyleClass: string
{
    case Danger = "sx-danger";
    case Warning = "sx-warning";
    case Info = "sx-info";
    case Success = "sx-success";
}

enum EnumField: string
{
    case Address = "address";
    case Admin = "admin";
    case Agreement = "agreement";
    case Avatar = "avatar";
    case BirthdayDay = "birthday_day";
    case BirthdayMon = "birthday_mon";
    case CatId = "cat_id";
    case Comment = "comment";
    case ContactName = "contact_name";
    case ContactPhone = "contact_phone";
    case DataSendMsg = "data_is_send";
    case DeliveryTo = "delivery_to";
    case Description = "description";
    case Email = "email";
    case Error = "error";
    case FIO = "fio";
    case Hash = "hash";
    case IP = "ip";
    case IsDisabled = "is_disabled";
    case Item = "item";
    case ItemId = "item_id";
    case Items = "items";
    case Limit = "limit";
    case ModeIsTest = "MODE_IS_TEST";
    case Msg = "msg";
    case Name = "name";
    case Offset = "offset";
    case Order = "order";
    case OrderId = "order_id";
    case Orders = "orders";
    case ParentId = "parent_id";
    case Password = "password";
    case PasswordConfirm = "password_confirm";
    case PhoneNumber = "phone_number";
    case PlaceDelivery = "place_delivery";
    case Pos = "pos";
    case Price = "price";
    case PrivacyPolicy = "privacy_policy";
    case Profile = "profile";
    case RequestedAgreement = "requested_agreement";
    case RequestedEmail = "requested_email";
    case RequestedPrivatePolicy = "requested_private_policy";
    case Role = "role";
    case Status = "status";
    case Styles = "styles";
    case Success = "success";
    case Title = "title";
    case Type = "type";
    case User = "user";
    case UserId = "user_id";
    case Users = "users";
}

// viewNames
enum EnumViewFile: string
{
    case Default = "";
    case ModuleBreakCrumbs = "module-breakcrumbs.php";
    case ModuleCartItem = "module-cart-item.php";
    case ModuleCatalogMenu = "module-catalog-menu.php";
    case ModuleCounter = "module-counter.php";
    case ModuleItem = "module-item.php";
    case ModuleNotice = "module-notice.php";
    case ModulePaginator = "module-paginator.php";
    case EmailMsgAndLink = "email-msg-and-link.php";
    case PageAccessDined = "page-access-dined.php";
    case PageAdm = "page-adm.php";
    case PageAdmCat = "page-adm-cat.php";
    case PageAdmCats = "page-adm-cats.php";
    case PageAdmItem = "page-adm-item.php";
    case PageAdmItems = "page-adm-items.php";
    case PageAdmOrder = "page-adm-order.php";
    case PageAdmOrders = "page-adm-orders.php";
    case PageAdmUser = "page-adm-user.php";
    case PageAdmUsers = "page-adm-users.php";
    case PageAgreement = "page-agreement.php";
    case PageCart = "page-cart.php";
    case PageCat = "page-cat.php";
    case PageCheckout = "page-checkout.php";
    case PageContacts = "page-contacts.php";
    case PageInfo = "page-info.php";
    case PageItem = "page-item.php";
    case PageLogin = "page-login.php";
    case PageMain = "page-main.php";
    case PageNotFound = "page-notfound.php";
    case PageOrder = "page-order.php";
    case PageOrderOk = "page-order_ok.php";
    case PagePrivacyPolicy = "page-privacy-policy.php";
    case PageProfile = "page-profile.php";
    case PageRecover = "page-recover.php";
    case PageRecoverCheck = "page-recover_check.php";
    case PageReg = "page-reg.php";
    case PageRegCheck = "page-reg_check.php";
    case PageRegOK = "page-reg_ok.php";
    case PageSearch = "page-search.php";
}

enum EnumStatusOrder: string
{
    case Created = "created";
    case Collected = "collected";
    case Finished = "finished";
}