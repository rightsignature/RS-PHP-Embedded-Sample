<?php
class Document extends ADOdb_Active_Record{ 
 
	// 
	// Callback function hook for when document gets a 'created' status from document_callback.php
	// 
	function createdCallback($http_body) {
		error_log("Document $this->id created");
		$xml = simplexml_load_string($http_body);
		
	}
	
	// 
	// Callback function hook for when document gets a 'viewed' status from document_callback.php
	//
	function viewedCallback($http_body) {
		error_log("Document $this->id viewed");
		$xml = simplexml_load_string($http_body);
	}
	
	// 
	// Callback function hook for when document gets a 'signed' status from document_callback.php
	//
	function completedCallback($http_body) {
		error_log("Document $this->id completed");
		$xml = simplexml_load_string($http_body);
	}
} {}

ADODB_Active_Record::ClassBelongsTo('document','user','user_id','id');

?>