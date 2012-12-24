<?php

require_once 'model.php';

$uri = $_SERVER['REQUEST_URI'];
if ($uri == '/index.php' || $uri == '/' || isset($_GET['state'])) {
    //list_friends();
	set_data();
} elseif (isset($_GET['user_id']) && isset($_GET['lat1']) && isset($_GET['lon1']) && isset($_GET['lat2']) && isset($_GET['lon2'])) {
	$res = get_data($_GET['user_id'], $_GET['lat1'], $_GET['lon1'], $_GET['lat2'], $_GET['lon2']);
	$res = json_encode($res);
	echo $res;
} elseif (isset($_GET['dest_id'])) {
    http_to_friends($_GET['src_id'], $_GET['dest_id']);
} else {
    header('Status: 404 Not Found');
    echo '<html><body><h1>ページが見つかりません</h1></body></html>';
}
