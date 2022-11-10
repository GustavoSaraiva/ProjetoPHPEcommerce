<?php

use \Ecommerce\Page;
use \Ecommerce\PageAdmin;
use \Ecommerce\Model\User;
use \Ecommerce\model\Product;
use \Ecommerce\Model\Category;

$app->get("/admin/products", function (){

    User::verifyLogin();

    $products = Product::listAll();
    $page = new PageAdmin();
    $page->setTpl("products", array("products"=>$products));


});

$app->get("/admin/products/create", function (){

        User::verifyLogin();

        $page = new PageAdmin();
        $page->setTpl("products-create");

});

$app->post("/admin/products/create", function(){

    User::verifyLogin();

    $product = new Product();

    $product->setValues($_POST);
    //var_dump($_POST);

    $product->save();

    header("Location: /admin/products");
    exit;

});

$app->get("/admin/products/{idproduct}", function ($res,$args,$idproduct){

    User::verifyLogin();

    $product = new Product();
    $val = $idproduct['idproduct'];
    $product->get($val);

    $page = new PageAdmin();
    $page->setTpl("products-update", array('product'=>$product->getValues()));

});

$app->post("/admin/products/{idproduct}", function ($res,$args,$idproduct){

    User::verifyLogin();

    $product = new Product();
    $val = $idproduct['idproduct'];
    $product->get($val);
    $product->setValues($_POST);
    $product->save();

    if(file_exists($_FILES['file']['tmp_name']) || is_uploaded_file($_FILES['file']['tmp_name']))
    {
        $product->setPhoto($_FILES["file"]);
    }
    header("Location: /admin/products");
    exit;

});

$app->get("/admin/products/{idproduct}/delete", function($request, $response, $idproduct){

    User::verifyLogin();

    $product = new Product();
    $val=(int)$idproduct['idproduct'];
    $product->get($val);
    $product->delete();
    header("Location: /admin/products");
    exit;

});
