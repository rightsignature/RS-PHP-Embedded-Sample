<?php
// 
// Imports RightSignature's Documents into local documents in database
// 
ob_start();
require_once($_SERVER['DOCUMENT_ROOT'] . "/config/common.inc.php");

// Calls API to get documents XML and parses it
$documents_xml = simplexml_load_string($rightsignature->getDocuments());

// Loops through each document node and copies guid
foreach($documents_xml->documents->document as $document_node) {
	$document = new Document();
	if ($document->load("rs_document_id=\"" . mysql_escape_string(trim((string)$document_node->guid)) . "\"")) {
		error_log("Found document with RS guid of" . (string)$document_node->guid . " will just update data");
	} else {
		error_log("Cannot find document with RS guid of" . (string)$document_node->guid . " importing...");
		$document->rs_document_id = (string)$document_node->guid;
		if ($document->save()) {
			error_log("successfully saved to $document->id");
		} else {
			error_log("cannot save document");
		}
	}
	
	// gets Document Details from RS for values in document components (Text fields, Date fields, etc...) and merge fields
	$document_details = $rightsignature->getDocumentDetails($document->rs_document_id);
	$xml = simplexml_load_string($document_details);
	// Process Details as necessary
	print_r($xml);
}
ob_flush();
?>