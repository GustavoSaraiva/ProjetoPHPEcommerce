<?php

use \Ecommerce\Page;
use \Ecommerce\PageAdmin;
use \Ecommerce\Model\User;
use \Ecommerce\Model\Category;




$app->get('/admin', function ($request, $response, $args) {

User::verifyLogin();

$page = new PageAdmin();
$page->setTpl("index");


});
$app->get('/admin/login', function ($request, $response, $args) {

$page = new PageAdmin([
"header" => false,
"footer" => false,
]);
$page->setTpl("login");


});

$app->post('/admin/login', function ($request, $response, $args){


User::login($_POST["login"],$_POST["password"]);
header("Location: /admin");
exit;


});

$app->get('/admin/logout', function ($request, $response, $args){

User::logout();

header("Location: /admin/login");
exit;

});