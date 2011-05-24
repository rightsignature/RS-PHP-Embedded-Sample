<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/config/common.inc.php");
if (isset($_REQUEST['template_id'])) {
	// Parse response XML
	$xml = simplexml_load_string($GLOBALS['HTTP_RAW_POST_DATA']);

	// updates local template in database
	$template = new Template();
	if ($template->load("id=\"" . mysql_escape_string(trim($_REQUEST['template_id'])) . "\"")){
		error_log("updating " . $_REQUEST['template_id'] . " with RS ID of " . $xml->guid);
		$template->rs_template_id = $xml->guid;
		$template->save();
	} else {
		error_log("cannot find template with " . $_REQUEST['template_id']);
	}
} else {
	error_log("Warning no template_id found in the url for the following Data:");
	error_log($GLOBALS['HTTP_RAW_POST_DATA']);
}

?>