<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/config/common.inc.php");

if (isset($_REQUEST["document_id"])) {
	// Parse response body
	$xml = simplexml_load_string($GLOBALS['HTTP_RAW_POST_DATA']);
	// Loads up document by document_id
	$document = new Document();
	if( $document->load("id=" . mysql_escape_string(trim($_REQUEST["document_id"]))) ) {
		// Compares XML's GUID with the GUID in database to authenicate request
		if ($document->rs_document_id == $xml->guid) {
			// checks status of Document and do a different action
			switch ((string)$xml->status) {
				case "created":
					$document->createdCallback($GLOBALS['HTTP_RAW_POST_DATA']);
					break;
				case "viewed":
					$document->viewedCallback($GLOBALS['HTTP_RAW_POST_DATA']);
					break;
				case "signed":
					$document->completeCallback($GLOBALS['HTTP_RAW_POST_DATA']);
					break;
			}
		} else {
			error_log("Local Document and XML rs_document_id do not match, ignoring request:");
			error_log($GLOBALS['HTTP_RAW_POST_DATA']);
		}
	} else {
		error_log("Cannot find document from 'document_id' params, ignoring request:");
		error_log($GLOBALS['HTTP_RAW_POST_DATA']);		
	}
	
} else {
	error_log("did not receive 'document_id' in parameters, ignoring request:");
	error_log($GLOBALS['HTTP_RAW_POST_DATA']);
}
?>