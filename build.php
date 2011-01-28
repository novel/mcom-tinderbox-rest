<?php

require_once 'RESTService.php';

$request_method = strtolower($_SERVER['REQUEST_METHOD']);
$request_headers = http_get_request_headers();

$RESTService = new RESTService(); 

$RESTService->check_auth($request_headers);

if ($request_method == "get") {
	if (in_array("id", $_GET)) 
		echo $RESTService->getBuild($_GET["id"]);	
	else
		echo $RESTService->listBuilds();
} else {
	echo "blah";
}

?>
