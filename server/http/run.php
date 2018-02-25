<?php
require "../../config/config.php";
require "../App.php";
$main = array_merge(require BASE_ROOT."/config/main.php",require BASE_ROOT."/server/http/config/main.php");
spl_autoload_register([App::class,'autoLoad']);
App::run(new \server\http\HttpServer(),$main);