<!DOCTYPE HTML>
<html>
	<head>
    	<title>HTTP ACCESS TO FACEBOOK FRIENDS</title>
		<link rel="stylesheet" type="text/css" href="./css/stylesheet.css" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	</head>
	<body>
    	<h1>HTTP ACCESS TO FACEBOOK FRIENDS</h1>
		<p>
			<?php
				foreach($user_friends[data] as $data){
					echo '<div class="friends"><img src="https://graph.facebook.com/'.$data['id'].'/picture" alt="'.$data['name'].' Profile Photo"><br></div>';
				}
			?>
		</p>
	</body>
</html>
