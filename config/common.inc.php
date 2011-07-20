<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/config/creds.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/classes/rightsignature.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/classes/main.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/classes/document.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/classes/template.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/classes/user.php");

// This is a HTTP wrapper that appends 'api-token' to Headers
$rightsignature = new RightSignature($api_secure_token);

$rightsignature->debug = $debug;

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . "/classes/helpers/session.inc.php");
?>
