<?php 

session_start();

require_once("vendor/autoload.php");

use Slim\Slim;

$app = new Slim();

$app->config('debug', true);

//Rotas a ver com o site:
require_once("site.php");
require_once("functions.php");

//Rotas do Adminstrativo:
require_once("admin.php");

//Rota do Administrativo do Usuário:
require_once("admin-users.php");

//Rota das Categorias:
require_once("admin-categories.php");

//Rota para Administração de produtos:
require_once("admin-products.php");



$app->run();

?>