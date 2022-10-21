<?php

// Include router class
require_once 'vendor/autoload.php';

use Steampixel\Route;

// Add base route (startpage)
Route::add('/',function(){
    //echo 'Welcome :-)';
    header('LOCATION: https://www.pratic-boutic.fr');
});

// admin file path
Route::add('/admin',function(){
    header('LOCATION: ../common/customerarea/index.php');
});

// admin directory path
Route::add('/admin/',function(){
    header('LOCATION: ../common/customerarea/index.php');
});

// superadmin file path
//Route::add('/superadmin',function(){
//    header('LOCATION: ../common/superadmin/index.php');
//});

// superadmin directory path
//Route::add('/superadmin/',function(){
//    header('LOCATION: ../common/superadmin/index.php');
//});

// boutic file path
Route::add('/([a-z0-9]+)',function($customer){
    header('LOCATION: ../common/index.php?method=' . 3 . '&customer=' . $customer);
});

// boutic directory path
Route::add('/([a-z0-9]+)/',function($customer){
    header('LOCATION: ../common/index.php?method=' . 3 . '&customer=' . $customer);
});

Route::run('/');

?>

