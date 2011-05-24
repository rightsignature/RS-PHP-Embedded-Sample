<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/config/creds.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/classes/oauth/rightsignature.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/classes/main.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/classes/document.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/classes/template.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/classes/user.php");

// First step is to initialize with your consumer key and secret.
$rightsignature = new RightSignature($consumer_key, $consumer_secret, $oauth_callback);

// Tries to retrieve access token and verifier from DB
$oauth_at = $DB->GetOne("SELECT value FROM settings WHERE name='rs_oauth_access_key'");
$oauth_as = $DB->GetOne("SELECT value FROM settings WHERE name='rs_oauth_access_secret'");

// If access token key and secret was found, load it
if (!empty($oauth_at) && !empty($oauth_as)) {
	$rightsignature->access_token = new OAuthConsumer($oauth_at, $oauth_as, 1);
}

$rightsignature->debug = $debug;

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . "/classes/helpers/session.inc.php");
?>
