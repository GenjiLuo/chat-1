<?php
require_once "./ChatServer.php";
require_once "./App.php";
require_once "./Di.php";
require_once "../vendor/autoload.php";
require_once "../config/config.php";
require_once "./config/key.php";
$main = array_merge( require "../config/main.php",require "./config/main.php");
$DI  =  new Di($main);
App::run($DI);

