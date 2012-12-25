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
//$access_token = 'AAAEexN3lBi8BAPEkBH5OpFZCVgJ93CjLSN4Y1uSe5olVtZAh9l35AbdKv39bGLieRHm43kJmX4ZBdCMCzuaBQOhpiPwiDvef8eb2V38xgZDZD';
//$access_token = 'AAAEexN3lBi8BAJx2PCHjxAbpQdVQ7iFM5aM2I9ZBv9863iZCdLKiCjSXIzH7ecwpfZAZClxGlRRjHAukiVflSoTzYudBoidnEAlyKNHyHAZDZD';
$access_token = 'AAAEexN3lBi8BAE3BwWeWxSGWuAsFL1J7alBPfegxqgPClFcfiGBcysuDW3ZCvN6RCRTTZBVITCuGBH8YIivncykJlsBXNabPDR5G4D0AZDZD';
$facebook->setAccessToken($access_token);
$uid = $facebook->getUser();

$user_friends = $facebook->api('/me/friends', 'GET');
//友達のチェックインデータの処理
$data = array();
foreach ($user_friends['data'] as $friend) {
		$fql = 'SELECT name, pic_square FROM user WHERE uid=' . $friend['id'];
		$results = $facebook->api(array(
								'method' => 'fql.query',
								'query' => $fql
								));
		$friendName = $results[0]['name'];
		//echo $friendName . ' さんのチェックイン情報を登録中...';
		echo $friendName . ' さんのチェックイン情報を登録中...';
		$friendCheckins = $facebook->api('/' . $friend['id'] .'/checkins?fields=id,message,place&limit=20', 'GET');

		foreach ($friendCheckins['data'] as $checkin) {
				$place = array();
				$place['obj_id'] = $checkin['id'];
				$place['friend_name'] = $friendName;
				$place['friend_pic'] = $results[0]['pic_square'];
				$place['place_name'] = $checkin['place']['name'];
				$place['place_id'] = $checkin['place']['id'];
				$place['lat'] = $checkin['place']['location']['latitude'];
				$place['lon'] = $checkin['place']['location']['longitude'];
				echo $place['place_name'];
				if (isset($checkin['message'])) {
						$place['message'] = $checkin['message'];
				} else {
						$place['message'] = '';
				}
				$placeData = $facebook->api('/' . $checkin['place']['id'], 'GET' );
				if (isset($placeData['website'])) {
						$place['web'] = $placeData['website'];
				} else {
						$place['web'] = '';
				}
				if (isset($placeData['phone'])) {
						$place['phone'] = $placeData['phone'];
				} else {
						$place['phone'] = '';
				}
				if(isset($placeData['location']['street'])) {
						$place['street'] = $placeData['location']['street'];
				} else {
						$palce['street'] = '';
				}

				//$data[] = $place;
				try {
						$sql = "INSERT INTO place_data VALUES (:obj_id, :user_id, :lat, :lon, :place_name, :place_id, :place_web, :place_phone, :place_street, :friend_name, :friend_pic, :message)";
						$stmt = $db->prepare($sql);
						$params = array(":obj_id" => $place['obj_id'], "user_id" => $uid,":lat" => $place['lat'], ":lon" => $place['lon'], ":place_name" => $place['place_name'], ":place_id" => $place['place_id'], ":place_web" => $place['web'], ":place_phone" => $place['phone'], ":place_street" => $place['street'], ":friend_name" => $place["friend_name"], ":friend_pic" => $place['friend_pic'], ":message" => $place["message"]);

						$flag = $stmt->execute($params);

						if ($flag){
								//echo $query;
						} else {
								echo "INSERT ERROR";
						}
				} catch(PDOException $e){
						echo $e;
				}
		}
}
$db = null;
