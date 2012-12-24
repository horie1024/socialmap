<?php
require_once("php_sdk/facebook.php");

define('DB_NAME', getenv('C4SA_MYSQL_DB'));
define('DB_USER', getenv('C4SA_MYSQL_USER'));
define('DB_PASSWORD', getenv('C4SA_MYSQL_PASSWORD'));
define('DB_HOST', getenv('C4SA_MYSQL_HOST'));

$dsn = 'mysql:dbname='.DB_NAME.';host='.DB_HOST.';charset=utf8;';
try{
	$db = new PDO($dsn, DB_USER, DB_PASSWORD);
}catch (PDOExeption $e){
	echo'Error:'.$e->getMessage();
	die();
}

$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$config = array();
$config['appId'] = '315305861908015';
$config['secret'] = '10a1372511de89d8c349de28ee1b801f';
$config['fileUpload'] = false; // optional
$facebook = new Facebook($config);
$access_token = 'AAAEexN3lBi8BAPEkBH5OpFZCVgJ93CjLSN4Y1uSe5olVtZAh9l35AbdKv39bGLieRHm43kJmX4ZBdCMCzuaBQOhpiPwiDvef8eb2V38xgZDZD';
$facebook->setAccessToken($access_token);
$uid = $facebook->getUser();


$user_friends = $facebook->api('/me/friends', 'GET');
			//友達のチェックインデータの処理
			$data = array();
            foreach ($user_friends['data'] as $friend) {
            	$friendName = $friend['name'];
                echo $friendName . ' さんのチェックイン情報を登録中...';
                $friendCheckins = $facebook->api('/' . $friend['id'] .'/checkins?fields=id,message,place&limit=10', 'GET');
            	foreach ($friendCheckins['data'] as $checkin) {
                	$place = array();
                	$place['obj_id'] = $checkin['id'];
                	$place['friend_name'] = $friend['name'];
                	$place['place_name'] = $checkin['place']['name'];
                    $place['place_id'] = $checkin['place']['id'];
                    $place['lat'] = $checkin['place']['location']['latitude'];
                	$place['lon'] = $checkin['place']['location']['longitude'];
                  if (isset($checkin['message'])) {
                    $place['message'] = $checkin['message'];
                  } else {
                    $place['message'] = '';
                  }
                  //$data[] = $place;
                  try {
                    $sql = "INSERT INTO palce_data VALUES (:obj_id, :user_id, :lat, :lon, :place_name, :place_id, :friend_name, :message)";
                    $stmt = $db->prepare($sql);
                    $params = array(":obj_id" => $place['obj_id'], "user_id" => $uid,":lat" => $place['lat'], ":lon" => $place['lon'], ":place_name" => $place['place_name'], ":place_id" => $place['place_id'], ":friend_name" => $place["friend_name"], ":message" => $place["message"]);
        
                    $flag = $stmt->execute($params);
                  
                    if ($flag){
                      //echo $query;
                    } else {
                      echo "INSERT ERROR";
                    }
                  } catch(PDOException $e){
                    //var_dump($e); 
                    echo $e;
                  }
              	}
			}

$db = null;
