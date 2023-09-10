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
const DIR_TEMPLATES = DIR_APP . "/views";
const DIR_CONTROLLERS = DIR_APP . "/controllers";
const DIR_CLASSES = DIR_APP . "/classes";
const DIR_REQUESTS = DIR_APP . "/requests";
const DIR_SERVICES = DIR_APP . "/services";

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
const FieldItem = "item";
const FieldPhoneNumber = "phone_number";
const FieldFIO = "fio";
const FieldDeliveryTo = "delivery_to";

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
const ViewPageRegOK = "page-reg_ok.php";
const ViewPageRegCheck = "page-reg_check.php";
const ViewPageSearch = "page-search.php";
const ViewPageOrder = "page-order.php";
const ViewPageOrderOk = "page-order-ok.php";
