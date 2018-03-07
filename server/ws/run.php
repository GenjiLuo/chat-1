<?php
require "../../config/config.php";
require BASE_ROOT."/core/App.php";

$dependence = array_merge(require BASE_ROOT . "/config/dependence.php", require BASE_ROOT . "/server/ws/config/dependence.php");
$component = array_merge(require BASE_ROOT."/config/component.php",require BASE_ROOT."/server/ws/config/component.php");
spl_autoload_register([\core\App::class, 'autoLoad']);
\core\App::run(new \server\ws\WsServer(), $dependence,$component);

