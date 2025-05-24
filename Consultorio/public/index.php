<?php

$request = $_SERVER['REQUEST_URI'];
$request = strtok($request, '?');

switch($request){
    case '/':
        require_once __DIR__.'/../src/views/public/welcome.php';
        break;
    case '/login':
        require_once __DIR__.'/login.php';
        break;
    case '/products':
        require_once __DIR__.'/../src/views/admin/products/index.php';
        break;
    case '/products/form':
        require_once __DIR__.'/../src/views/admin/products/form.php';
        break;
    case '/logout':
        require_once __DIR__.'/../src/controllers/LogoutController.php';
        break;
    case '/products/add': 
        require_once __DIR__.'/../src/controllers/ProductsController.php';
        addProductHandler(); 
        break;
    case '/products/edit':
        require_once __DIR__.'/../src/views/admin/products/edit.php';
        break;
    case '/products/update':
        require_once __DIR__.'/../src/controllers/ProductsController.php';
        updateProductHandler();
        break;
    case '/products/delete':
        require_once __DIR__.'/../src/controllers/ProductsController.php';
        deleteProductHandler();
        break;
    case '/genres/add':
        require_once __DIR__.'/../src/controllers/ProductsController.php';
        addGenreHandler();
        break;
    case '/details':
        include __DIR__.'/../src/views/public/details/details.php';
        break; 
    case '/biblioteca':
        include __DIR__.'/../src/views/public/biblioteca.php';
        addGenreHandler();
        break;  
    default:
    require_once __DIR__.'/errores.php';
        break;
}