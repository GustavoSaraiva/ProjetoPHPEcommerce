<?php

use \Ecommerce\Page;
use \Ecommerce\PageAdmin;
use \Ecommerce\Model\User;
use \Ecommerce\Model\Category;


$app->get("/admin/users", function($request, $response, $args){

    User::verifyLogin();

    $users = User::listAll();
    $page = new PageAdmin();
    $page->setTpl("users", array("users"=>$users));
});

$app->get("/admin/users/create", function($request, $response, $args){

    User::verifyLogin();

    $page = new PageAdmin();
    $page->setTpl("users-create");
});

$app->get("/admin/users/{iduser}/delete", function($request, $response, $iduser){

    User::verifyLogin();

    $user = new User();
    $val=(int)$iduser['iduser'];
    $user->get($val);
    $user->delete();
    header("Location: /admin/users");
    exit;

});

$app->get("/admin/users/{iduser}", function($request, $response, $iduser){
    User::verifyLogin();
    $user = new User();
    $val = (int)$iduser["iduser"];
    $user->get($val);
    //var_dump($user);
    $page = new PageAdmin();
    $page->setTpl("users-update", array(
        "user"=>$user->getValues()
    ));
});

$app->post("/admin/users/create", function($request, $response, $args){

    User::verifyLogin();
    $user = new User();

    $_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;

    $user->setValues($_POST);



    $user->save();
    //var_dump($user);
    header("Location: /admin/users");
    exit;

});

$app->post("/admin/users/{iduser}", function($request, $response, $iduser){

    User::verifyLogin();

    $user = new User();
    $val = (int)$iduser['iduser'];
    $user->get($val);
    $user->setValues($_POST);
    $user->isAdmin($_POST);
    $user->update();
    //var_dump($_POST);
    header("Location: /admin/users");
    exit;

});


$app->get("/admin/forgot", function (){
    $page = new PageAdmin(["header"=>false,"footer"=>false]);
    $page->setTpl("forgot");
});

$app->post("/admin/forgot", function (){
    $user = User::getForgot($_POST["email"]);
    //var_dump($_POST["email"]);
    header("Location: /admin/forgot/sent");
    exit;

});

$app->get("/admin/forgot/sent", function(){
    $page = new PageAdmin(["header"=>false,"footer"=>false]);
    $page->setTpl("forgot-sent");


});

$app->get("/admin/forgot/reset", function (){

    $user = User::validForgotDecrypt($_GET["code"]);

    $page = new PageAdmin(["header"=>false,"footer"=>false]);
    $page->setTpl("forgot-reset", array("name"=>$user["desperson"], "code"=>$_GET["code"]));

});

$app->post("/admin/forgot/reset", function(){

    $forgot = User::validForgotDecrypt($_POST["code"]);
    User::setForgotUsed($forgot["idrecovery"]);
    $user = new User();
    $user->get((int)$forgot["iduser"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT, ["cost"=>12]);
    $user->setPassword($password);
    $page = new PageAdmin(["header"=>false,"footer"=>false]);
    $page->setTpl("forgot-reset-success");



});