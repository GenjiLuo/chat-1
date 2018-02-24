<?php
require "../../config/config.php";
require "../App.php";
require_once "../../vendor/autoload.php";
$main = array_merge(require BASE_ROOT."/config/main.php",require BASE_ROOT."/server/http/config/main.php");
App::run("server\http\HttpServer",$main);