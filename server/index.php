<?php
define("BASE_ROOT",dirname(__DIR__));
require_once './App.php';
$config = require('config/config.php');
$app = new App($config);
