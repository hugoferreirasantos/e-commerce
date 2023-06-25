<?php

//Use
use Hcode\Page;


//Rotas do site:
$app->get('/', function() {
    
    $page = new Page();

    $page->setTpl("index");
	
});



?>