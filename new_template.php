<?php
ob_start();
require_once($_SERVER['DOCUMENT_ROOT'] . "/config/common.inc.php");

// Create local template in database 
$template = new Template();
$template->user_id = $_SESSION['user_id'];

if ($template->save()) {

	// Create an array for common mergefields for template
	$mergefields = array('Address', 'User ID');

	// Create an array of selectable role names for template
	$roles = array('Owner', 'Leaser', "Leasee", "Manager");

	// Create an associated array for template tags. Here we record the user's id so we can filter it out in future API calls 
	// and add our local template id so we can have an association
	$tags = array('user' => $_SESSION['user_id'], 'template_id' => $template->id);

	// URL for RightSignature to POST the template information to when the template has been created. 
	// This URL contains the our template ID so we will know which one to modify.
	// We can also do other stuff like add our own token in the URL to authenicate RightSignature's
	// request to the callback location (TODO: this sentence). Or save the redirect token and use that to check.
	// If callbackURL is NULL, the default callback from RightSignature's account setting is used.
	$callbackURL = "$siteURL/new_template_callback.php?template_id=$template->id";

	// URL for RightSignature to redirect user after they create the template on RightSignature
	$redirectURL = "$siteURL/";

	// Call API to generate a Redirect Token to be able to create the Template under our RightSignature Account
	$redirectTokenURL = $rightsignature->createTemplateTokenURL($mergefields, $roles, $tags, $callbackURL, $redirectURL);

	header("Location: $redirectTokenURL");
} else {
	$_SESSION["error_message"] = "There was a problem in setting up your new template. Please try again in a few mintues.";
	header("Location: $siteURL/");
}

ob_flush();
?>