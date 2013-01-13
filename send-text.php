<?php
	$url = 'http://www.gvmax.com/api/send';
	$data = array('action' => 'send', 'email' => 'ucblibrarytimes@gmail.com', 'apikey' => '047f88043fc14908a4fb37c45bf2fca6', 'number' => '14438252032', 'text' => 'This is the text message');

	// use key 'http' even if you send the request to https://...
	$options = array('http' => array('method'  => 'POST','content' => http_build_query($data)));
	$context  = stream_context_create($options);
	$result = file_get_contents($url, false, $context);

	echo 'break' . PHP_EOL;
	var_dump($result);
?>