<?php
function common_styles() {
	echo '<link rel="stylesheet" href="css/blueprint/screen.css" type="text/css" media="screen, projection">
	<link rel="stylesheet" href="css/blueprint/print.css" type="text/css" media="print">
	<!--[if lt IE 8]>
	  <link rel="stylesheet" href="css/blueprint/ie.css" type="text/css" media="screen, projection">
	<![endif]-->
	<link rel="stylesheet" href="css/common.css" type="text/css">
	';
}

function login_bar() {
	echo '<div class="login-bar">';
	echo "<a class=\"button\" href=\"/\">Home</a> ";
	if (empty($_SESSION["user_id"])) {
		echo "<span class=\"right\">You are not logged in. <a class=\"button\" href=\"/login.php\">Log In</a> | <a class=\"button\" href=\"/signup.php\">Sign Up</a></span>";
	} else {
		echo " | <a class=\"button\" href=\"/list_documents.php\">My Documents</a>";
		echo " | <a class=\"button\" href=\"/list_templates.php\">My Templates</a>";
		echo "<span class=\"right\">You are logged in. <a class=\"button\" href=\"/sessions/logout_user.php\">Log Out</a></span>";
	}
	echo '</div>';
}

function message_div() {
	if (!empty($_SESSION["notice_message"])) {
		echo '<div class="span-23 notice">';
		echo $_SESSION["notice_message"];
		echo '</div>';
		$_SESSION["notice_message"] = "";
	}
	if (!empty($_SESSION["success_message"])) {
		echo '<div class="span-23 success">';
		echo $_SESSION["success_message"];
		echo '</div>';
		$_SESSION["success_message"] = "";
	}
	if (!empty($_SESSION["error_message"])) {
		echo '<div class="span-23 error">';
		echo $_SESSION["error_message"];
		echo '</div>';
		$_SESSION["error_message"] = "";
	}
}
?>