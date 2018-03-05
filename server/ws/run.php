<?php
require "../../config/config.php";
require "../App.php";
$main = array_merge(require BASE_ROOT . "/config/main.php", require BASE_ROOT . "/server/ws/config/main.php");
spl_autoload_register([App::class, 'autoLoad']);
App::run(new \server\ws\WsServer(), new \server\Di($main));