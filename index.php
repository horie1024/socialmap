?<?php

require_once 'model.php';

$uri = $_SERVER['REQUEST_URI'];
if ($uri == '/index.php' || $uri == '/' || isset($_GET['state'])) {
    list_friends();
} elseif ($uri === 'getData' && isset($_GET['user_id'] && isset($_GET['lat1'] && isset($_GET['lon1'] && isset($_GET['lat2'] && isset($_GET['lon2'])) )))) {
	//res_data();
	$res = array();
	$res['lat'] = 45.00;
	$res['lon'] = 140.00;
	$res['name'] = 'hoge';
	$res['message'] = 'test';
	$res = json_encode($res);
	echo $res;
} elseif (isset($_GET['dest_id'])) {
    http_to_friends($_GET['src_id'], $_GET['dest_id']);
} else {
    header('Status: 404 Not Found');
    echo '<html><body><h1>ページが見つかりません</h1></body></html>';
}
