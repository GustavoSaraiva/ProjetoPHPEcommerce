<?php

use \Ecommerce\Page;
use \Ecommerce\PageAdmin;
use \Ecommerce\Model\User;
use \Ecommerce\Model\Category;

$app->get("/category/{idcategory}", function($response, $argument, $idcategory){

    $category = new Category();
    $val = (int)$idcategory['idcategory'];
    $category->get($val);
    $page = new Page();
    $page->setTpl("category", ['category'=>$category->getValues(), 'products' => []]);


});