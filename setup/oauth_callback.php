<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/config/common.inc.php");

$oauth_request_token = $_REQUEST["oauth_token"];
$oauth_verifier = $_REQUEST["oauth_verifier"];

if ($oauth_request_token && $oauth_verifier) {
	try {
		// Check if request token is the same as the one in db
		$request_key = $DB->GetOne("SELECT value FROM settings WHERE name='rs_oauth_request_key'");
		$request_secret = $DB->GetOne("SELECT value FROM settings WHERE name='rs_oauth_request_secret'");
		if (!empty($request_key) && $request_key == $oauth_request_token) {
			// Use request token from settings
			$rightsignature->request_token = new OAuthConsumer($request_key, $request_secret, 1);
			$rightsignature->oauth_verifier = $oauth_verifier;
			$rightsignature->getAccessToken();

			// Saves access token key and secret
			$DB->Connect();
			$DB->Execute("INSERT INTO settings (name, value) VALUES('rs_oauth_access_key', '" . $rightsignature->access_token->key . "') ON DUPLICATE KEY UPDATE name='rs_oauth_access_key', value='" . $rightsignature->access_token->key . "'");
			$DB->Execute("INSERT INTO settings (name, value) VALUES('rs_oauth_access_secret', '" . $rightsignature->access_token->secret . "') ON DUPLICATE KEY UPDATE name='rs_oauth_access_secret', value='" . $rightsignature->access_token->secret . "'");
			$DB->Execute("INSERT INTO settings (name, value) VALUES('rs_oauth_verifier', '" . $oauth_verifier . "') ON DUPLICATE KEY UPDATE name='rs_oauth_verifier', value='" . $oauth_verifier . "'");
			echo "Access token and verifier saved";
		} else {
			echo "Request token key does not match one in settings";
		}
	} catch (exception $e) {
		echo "Error";
		print_r($e);
	}
} else {
	echo "Did not receive oauth token or verifier";
}
?>