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
define("DIR_APP", dirname(__FILE__)."/..");
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