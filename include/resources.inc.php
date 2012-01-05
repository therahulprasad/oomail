<?php
function linkSize($link) {
	//$remoteFile = 'http://us.php.net/get/php-5.2.10.tar.bz2/from/this/mirror';
	$remoteFile = $link;
	//echo $remoteFile;
	$ch = curl_init($remoteFile);
	curl_setopt($ch, CURLOPT_NOBODY, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); //not necessary unless the file redirects (like the PHP example we're using here)
	$data = curl_exec($ch);
	curl_close($ch);
	
	if ($data === false) {
	  //echo 'cURL failed';
	  return -2;
	  //exit;
	}
	$contentLength = 'unknown';
	$status = 'unknown';
	if (preg_match('/^HTTP\/1\.[01] (\d\d\d)/', $data, $matches)) {
	  $status = (int)$matches[1];
	}
	if (preg_match('/Content-Length: (\d+)/', $data, $matches)) {
	  $contentLength = (int)$matches[1];
	}
	if (preg_match('#Content-Type: (\w+)/(\w+)#', $data, $matches)) {
		$contentType = $matches[1] . "/" . $matches[2];
	}
	
	$return['size'] = $contentLength;
	$return['status'] = $status;
	$return['type'] = $contentType;
	
	if($contentLength == 'unknown' || $status == 'unknown') {
		return -1;
	} else {
		return $return;
	}
}
?>