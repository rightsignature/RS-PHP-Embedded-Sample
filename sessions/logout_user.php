<?php
ob_start();
require_once($_SERVER['DOCUMENT_ROOT'] . "/config/common.inc.php");

$_SESSION["user_id"] = NULL;
$_SESSION["success_message"] = "Successfully Logged Out";
header("Location: $siteURL/");

ob_flush();
?>