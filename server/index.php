<?php
require_once "./ChatServer.php";
require_once "./App.php";

require_once "../vendor/autoload.php";
require_once "../config/config.php";
require_once "../config/key.php";
$main = require "../config/main.php";

$app = new App($main);
ChatServer::init($app);
