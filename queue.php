<?php

require_once 'RESTService.php';

$request_method = strtolower($_SERVER['REQUEST_METHOD']);
$request_headers = http_get_request_headers();

$RESTService = new RESTService(); 

$RESTService->check_auth($request_headers);

if ($request_method == "get") {
	if (in_array("id", $_GET)) {
		//echo $RESTService->get($_GET["id"]);

		echo $RESTService->getQueueEntry($_GET["id"]);
	} else
		echo $RESTService->listQueueEntries();
} else if ($request_method == "put") {
	$request_body = http_get_request_body();

	/*echo $request_body;*/

	$entry_object = json_decode($request_body)->entry;

	echo $RESTService->addQueueEntry($entry_object->build->id,
		$entry_object->priority,
		$entry_object->portdirectory,
		$entry_object->email_on_completion);
}

?>
