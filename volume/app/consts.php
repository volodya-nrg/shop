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
const FieldError = "error";
const FieldRequestedEmail = "requested_email";
const FieldRequestedAgreement = "requested_agreement";
const FieldRequestedPrivatePolicy = "requested_private_policy";
const FieldMsg = "msg";
const FieldType = "type";
const FieldDataSendMsg = "data_is_send";
const FieldSuccess = "success";
const FieldHash = "hash";
const FieldStyles = "styles";
const FieldItems = "items";
const FieldItem = "item";
const FieldPhoneNumber = "phone_number";
const FieldFIO = "fio";
const FieldDeliveryTo = "delivery_to";
const FieldAddress = "address";
const FieldProfile = "profile";
const FieldAdmin = "admin";
const FieldModeIsTest = "MODE_IS_TEST";
const FieldItemId = "item_id";
const FieldName = "name";
const FieldSlug = "slug";
const FieldCatId = "cat_id";
const FieldDescription = "description";
const FieldPrice = "price";
const FieldIsDisabled = "is_disabled";
const FieldUpdatedAt = "updated_at";
const FieldCreatedAt = "created_at";
const FieldParentId = "parent_id";
const FieldPos = "pos";
const FieldLimit = "limit";
const FieldOffset = "offset";
const FieldUserId = "user_id";

// viewNames
const ViewModuleCounter = "module-counter.php";
const ViewModuleItem = "module-item.php";
const ViewModuleCartItem = "module-cart-item.php";
const ViewModuleNotice = "module-notice.php";
const ViewModuleBreakCrumbs = "module-breakcrumbs.php";
const ViewModuleCatalogMenu = "module-catalog-menu.php";
const ViewModulePaginator = "module-paginator.php";
const ViewPageAdm = "page-adm.php";
const ViewPageAdmItems = "page-adm-items.php";
const ViewPageAdmItem = "page-adm-item.php";
const ViewPageAdmCats = "page-adm-cats.php";
const ViewPageAdmCat = "page-adm-cat.php";
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
const ViewPageAccessDined = "page-access-dined.php";
const ViewPagePrivacyPolicy = "page-privacy-policy.php";
const ViewPageProfile = "page-profile.php";
const ViewPageRecover = "page-recover.php";
const ViewPageRecoverCheck = "page-recover_check.php";
const ViewPageReg = "page-reg.php";
const ViewPageRegOK = "page-reg_ok.php";
const ViewPageRegCheck = "page-reg_check.php";
const ViewPageSearch = "page-search.php";
const ViewPageOrder = "page-order.php";
const ViewPageOrderOk = "page-order_ok.php";
const ViewEmailMsgAndLink = "email-msg-and-link.php";