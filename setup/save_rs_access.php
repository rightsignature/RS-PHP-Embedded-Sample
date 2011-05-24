<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "/config/common.inc.php");

	// Tries to retrieve access token and verifier from DB
	$oauth_at = $DB->GetOne("SELECT value FROM settings WHERE name='rs_oauth_token'");
	$oauth_verifier = $DB->GetOne("SELECT value FROM settings WHERE name='rs_oauth_verifier'");
	$rightsignature->access_token = $oauth_at;
	$rightsignature->oauth_verifier = $oauth_verifier;
	
	// Now we retrieve a request token. It will be set as $rightsignature->request_token
	$rightsignature->getRequestToken();
	$DB->Connect();
	$DB->Execute("INSERT INTO settings (name, value) VALUES('rs_oauth_request_key', '" . $rightsignature->request_token->key . "') ON DUPLICATE KEY UPDATE name='rs_oauth_request_key', value='" . $rightsignature->request_token->key . "'");
	$DB->Execute("INSERT INTO settings (name, value) VALUES('rs_oauth_request_secret', '" . $rightsignature->request_token->secret . "') ON DUPLICATE KEY UPDATE name='rs_oauth_request_secret', value='" . $rightsignature->request_token->secret . "'");
	
	
	// With a request token in hand, we can generate an authorization URL, which we'll direct the user to
	// echo "Authorization URL: " . "<a href=\"" . $rightsignature->generateAuthorizeUrl() . "\"> click here to login</a>\n";
	echo $rightsignature->generateAuthorizeUrl();

	// After authorizing access, the callback location with have a oauth_verifier GET parameter,
	// set the verifier here...
	// echo "Enter the oauth_verifier GET param value to proceed...\n";
	// $handle = fopen("php://stdin", "r");
	// $verifier = trim(fgets(STDIN));

	// $rightsignature->oauth_verifier = $verifier;
	// $rightsignature->getAccessToken();
	// 
	// // Access token...
	// print_r($rightsignature->access_token);
	// 
	// save_access_token_and_verifier($rightsignature->access_token, $rightsignature->oauth_verifier);
	// 
	// // If already retrieved access token...
	// // $rightsignature->access_token = new OAuthConsumer($access_token, $access_token_secret, 1);
	// 
	// print_r($rightsignature->getDocuments());
	// print_r($rightsignature->testPost());

?>
