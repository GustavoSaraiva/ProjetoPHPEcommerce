<?php

use \Ecommerce\Page;
use \Ecommerce\PageAdmin;
use \Ecommerce\Model\User;
use \Ecommerce\Model\Category;
use \Ecommerce\Model\Product;


$app->get('/', function ($request, $response, $args) {

    $products = Product::listAll();

    $page = new Page();
    $page->setTpl("index", ['products'=>Product::checkList($products)]);


});