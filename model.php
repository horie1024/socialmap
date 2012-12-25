<?php
config_init();
connect_database();
$config = array();
$config['appId'] = '315305861908015';
$config['secret'] = '10a1372511de89d8c349de28ee1b801f';
$config['fileUpload'] = false; // optional
$facebook = new Facebook($config);
$db;

$uid = $facebook->getUser();

function config_init(){
	mb_language("uni");
	mb_internal_encoding("utf-8");
	mb_http_input("auto");
	mb_http_output("utf-8");
	require_once("php_sdk/facebook.php");
}
//DBへの接続
function connect_database(){
	define('DB_NAME', getenv('C4SA_MYSQL_DB'));
	define('DB_USER', getenv('C4SA_MYSQL_USER'));
	define('DB_PASSWORD', getenv('C4SA_MYSQL_PASSWORD'));
	define('DB_HOST', getenv('C4SA_MYSQL_HOST'));

	$dsn = 'mysql:dbname='.DB_NAME.';host='.DB_HOST.';charset=utf8;';
	try{
		$GLOBALS['db'] = new PDO($dsn, DB_USER, DB_PASSWORD);
	}catch (PDOExeption $e){
		print('Error:'.$e->getMessage());
		die();
	}
}

function get_data ($user_id, $lat1, $lon1, $lat2, $lon2,$limit) {
  try{
	$sql = 'SELECT * FROM place_data WHERE (lat BETWEEN "'. $lat1 .'" and "' . $lat2 . '") AND (lon BETWEEN "' . $lon1 . '" and "' . $lon2 . '") LIMIT 0, ' . $limit;
    $exec = $GLOBALS['db']->query($sql);
    $result = array();
	while ($result[] = $exec->fetch(PDO::FETCH_ASSOC));
    }catch (PDOExeption $e){
    echo $e;
  }
	return $result;
}

function get_place_data ($place_id) {
  try {
    $sql = 'SELECT * FROM place_data WHERE place_id=' . $place_id;
    $exec = $GLOBALS['db']->query($sql);
    $result = array();
	while ($result[] = $exec->fetch(PDO::FETCH_ASSOC));
  } catch (PDOExeption $e) {
    echo $e;
  }
  return $result;
}

function list_friends(){
	$param = array(
		'scope' => 'user_about_me,friends_about_me,user_relationships,friends_relationships,friends_birthday,publish_stream,user_status,friends_status,user_checkins,friends_checkins',
		'redirect_uri' => 'http://cclu2l6-aay-app000.c4sa.net/index.php'
	);
	if($GLOBALS['uid']){
		try {
			//友達一覧取得
            $access_token = $GLOBALS['facebook']->getAccessToken();
            echo $access_token;
          	return false;
			$user_friends = $GLOBALS['facebook']->api('/me/friends', 'GET');
			//友達一覧表示処理
            require 'templates/list.php';

		} catch(FacebookApiException $e){
       		login_to_fb($param);
	        error_log($e->getType());
        	error_log($e->getMessage());
   		}   
   	} else {
      	login_to_fb($param);
    }
}

function login_to_fb($param){
	$login_url = $GLOBALS['facebook']->getLoginUrl($param);
	//login.phpも作成(あとで)
	echo 'Please <a href="' . $login_url . '">login.</a>';
}
