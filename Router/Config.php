<?php

include_once "../vendor/autoload.php";


use Router\Web;

$route = new Web(
    $_SERVER["REQUEST_METHOD"], 
    $_SERVER["REQUEST_URI"] ?? "/"
);

$route->get("/", function(){
    
    Web::View("login@index.html");
});

$route->post("/","App\\Controller\\teste@postMethod");



$route->run();