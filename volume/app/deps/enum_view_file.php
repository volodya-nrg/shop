<?php declare(strict_types=1);

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
    case ModuleListItem = "module-list-item.php";
    case ModuleAdmList = "module-adm-list.php";
    case ModuleTabs = "module-tabs.php";
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
    case PageAdmEtc = "page-adm-etc.php";
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