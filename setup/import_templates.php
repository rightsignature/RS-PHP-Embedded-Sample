<?php
// 
// Imports RightSignature's Templates into local templates in database
// 
ob_start();
require_once($_SERVER['DOCUMENT_ROOT'] . "/config/common.inc.php");

// Calls API to get templates XML and parses it
$templates_xml = simplexml_load_string($rightsignature->getTemplates());

// Loops through each template node and copies guid

foreach($templates_xml->templates->template as $template_node) {
	$template = new Template();
	if ($template->load("rs_template_id=\"" . mysql_escape_string(trim((string)$template_node->guid)) . "\"")) {
		error_log("Found template with RS guid of" . (string)$template_node->guid . " will not import");
	} else {
		error_log("Cannot find template with RS guid of" . (string)$template_node->guid . " importing...");
		$template->rs_template_id = (string)$template_node->guid;
		if ($template->save()) {
			error_log("successfully saved to $template->id");
		} else {
			error_log("cannot save template");
		}
	}
}
ob_flush();
?>