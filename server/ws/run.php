<?php
require "../../config/config.php";
require "../App.php";
$dependence = array_merge(require BASE_ROOT . "/config/dependence.php", require BASE_ROOT . "/server/ws/config/dependence.php");
spl_autoload_register([App::class, 'autoLoad']);
App::run(new \server\ws\WsServer(), new \server\ws\Router(),$dependence);