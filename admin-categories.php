<?php

use \Ecommerce\Page;
use \Ecommerce\PageAdmin;
use \Ecommerce\Model\User;
use \Ecommerce\Model\Category;

$app->get("/admin/categories", function(){

    User::verifyLogin();

    $categories = Category::listAll();
    $page = new PageAdmin();
    $page->setTpl("categories",['categories'=>$categories]);
});

$app->get("/admin/categories/create", function(){

    User::verifyLogin();

    $page = new PageAdmin();
    $page->setTpl("categories-create");
});

$app->post("/admin/categories/create", function(){
    $category = new Category();
    $category->setValues($_POST);
    $category->save();

    header('Location: /admin/categories');
    exit;
});

$app->get("/admin/categories/{idcategory}/delete", function($request, $response, $idcategory){

    $category = new Category();
    $val=(int)$idcategory['idcategory'];
    $category->get($val);
    $category->delete();
    header('Location: /admin/categories');
    exit;

});

$app->get("/admin/categories/{idcategory}", function($request, $response, $idcategory){

    User::verifyLogin();

    $category = new Category();
    $val=(int)$idcategory['idcategory'];
    $category->get($val);
    $page = new PageAdmin();
    $page->setTpl("categories-update", ['category'=>$category->getValues()]);

});

$app->post("/admin/categories/{idcategory}", function($response, $arguments, $idcategory){

    $category = new Category();
    $val = (int)$idcategory['idcategory'];
    $category->get($val);
    $category->setValues($_POST);
    $category->save();

    header('Location: /admin/categories');
    exit;

});
