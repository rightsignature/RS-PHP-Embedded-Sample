<?php
ob_start();
require_once($_SERVER['DOCUMENT_ROOT'] . "/config/common.inc.php");

$user = new User();
if ($user->load("email=\"" . mysql_escape_string(trim($_POST["user_email"])) . "\"")) {
	$_SESSION["user_id"] = $user->id;
	$_SESSION["success_message"] = "Successfully Logged In";
	header("Location: $siteURL/");
} else {
	$_SESSION["error_message"] = "Error Logging In, please make sure the information is correct.";
	header("Location: $siteURL/login.php");
}

ob_flush();
?>