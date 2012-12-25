<?php
require_once 'model.php';

$uri = $_SERVER['REQUEST_URI'];
if ($uri == '/index.php' || $uri == '/' || isset($_GET['state'])) {
    list_friends();
} elseif (isset($_GET['user_id']) && isset($_GET['lat1']) && isset($_GET['lon1']) && isset($_GET['lat2']) && isset($_GET['lon2']) && isset($_GET['limit'])) {
	$res = get_data($_GET['user_id'], $_GET['lat1'], $_GET['lon1'], $_GET['lat2'], $_GET['lon2'], $_GET['limit']);
	$res = json_encode($res);
	echo $res;
} elseif (isset($_GET['place_id'])) {
  $res = get_place_data($_GET['place_id']);
  $res = json_encode($res);
  echo $res;
} else {
    header('Status: 404 Not Found');
    echo '<html><body><h1>ページが見つかりません</h1></body></html>';
}
