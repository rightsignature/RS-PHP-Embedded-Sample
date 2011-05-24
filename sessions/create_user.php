<?php
ob_start();
require_once($_SERVER['DOCUMENT_ROOT'] . "/config/common.inc.php");

$user = new User();
$user->name = mysql_escape_string(trim($_POST["user_name"]));
$user->email = mysql_escape_string(trim($_POST["user_email"]));
if ($user->save()) {
	$_SESSION["user_id"] = $user->id;
	$_SESSION["success_message"] = "Successfully Created Account!";
	header("Location: $siteURL/");
} else {
	$_SESSION["error_message"] = "Cannot create Account, please make sure the information is correct.";
	header("Location: $siteURL/signup.php");
}

ob_flush();
?>