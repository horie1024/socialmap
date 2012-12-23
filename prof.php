<?php
require_once("src/facebook.php");

$config = array();
$config[‘appId’] = '315305861908015';
$config[‘secret’] = '';
$config[‘fileUpload’] = false;

$facebook = new Facebook($config);
var_dump($facebook);
