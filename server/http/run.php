<?php
require "../../config/config.php";
require BASE_ROOT."/core/App.php";

$dependence = array_merge(require BASE_ROOT."/config/dependence.php",require BASE_ROOT."/server/http/config/dependence.php");
$component = array_merge(require BASE_ROOT."/config/component.php",require BASE_ROOT."/server/http/config/component.php");
spl_autoload_register([\core\App::class,'autoLoad']);
\core\App::run(new \server\http\HttpServer(),$dependence,$dependence);
