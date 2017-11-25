<?php
require_once "../config/config.php";
require_once "./config/config.php";
require_once "./App.php";
require_once BASE_ROOT."/vendor/autoload.php";
$main = array_merge( require "../config/main.php",require "./config/main.php");
require_once "./Di.php";
$DI = new Di($main);
App::init($DI);




