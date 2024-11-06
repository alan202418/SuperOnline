<?php

define("KEY_TOKEN", "APP.wqc-354*");
define("TOKEN_MP" , "APP_USR-3731307413845799-090518-0eb507d5c3e98c3df503cadd8d1fb797-1183108534");
define("MONEDA", "$");
session_start();

$num_cart = 0;
if(isset($_SESSION['carrito']['productos'])){
    $num_cart = count($_SESSION['carrito']['productos']);
}

?>