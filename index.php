<?php

// Include router class
include('route/Route.php');

// Add base route (startpage)
Route::add('/',function(){
    //echo 'Welcome :-)';
    header('LOCATION: https://www.pratic-boutic.fr');
});

// boutic file path 
Route::add('/([a-z0-9]+)',function($customer){
    header('LOCATION: ../common/carte.php?method=' . 3 . '&customer=' . $customer);
});

// boutic directory path 
Route::add('/([a-z0-9]+)/',function($customer){
    header('LOCATION: ../common/carte.php?method=' . 3 . '&customer=' . $customer);
});

Route::run('/');

?>

