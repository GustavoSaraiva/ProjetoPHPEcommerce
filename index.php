<?php

session_start();

require_once("vendor/autoload.php");

use \Slim\App;
use \Ecommerce\Page;
use \Ecommerce\PageAdmin;
use \Ecommerce\Model\User;
use \Ecommerce\Model\Category;

// Create and configure Slim app
$config = ['settings' => [
    'addContentLengthHeader' => false,
    'displayErrorDetails' => true,
    "auto_escape"   => false,
]];

$app = new App($config);

// Define app routes


require_once("site.php");
require_once("admin.php");
require_once("admin-users.php");
require_once("admin-categories.php");
require_once("categories.php");
require_once("admin-products.php");


// Run app
$app->run();