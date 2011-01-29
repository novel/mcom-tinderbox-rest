<?php

require_once 'RESTService.php';

$request_method = strtolower($_SERVER['REQUEST_METHOD']);
$request_headers = http_get_request_headers();

$RESTService = new RESTService(); 

$RESTService->check_auth($request_headers);

if ($request_method == "get") {
	print $RESTService->getLatestBuildPorts(10, null);
} else {
	echo "blah";
}

?>
