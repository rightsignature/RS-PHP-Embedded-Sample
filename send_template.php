<?php
ob_start();
require_once("config/common.inc.php");

// Get number of roles
$numRoles = count($_POST["role_names"]);
$roles = array();
$mergefields = array();

// Check if guid is set in session
if (isset($_SESSION["current_template"])) {
	// Error URL
	$errorURL = "$siteURL/select_template.php?guid=" . $_SESSION["current_template"];

	// Prepackages the Template to convert it into a sendable Document
	$guid = $rightsignature->prepackageTemplate(array($_SESSION["current_template"]));

	if (empty($guid)) {	
		$_SESSION["error_message"] = "Error generating Document, please retry in a few minutes";
		// Redirect to Select Template with error message
		header("Location: $errorURL");
		exit;
	}

	// create associated array for roles
	for ($i=0; $i < $numRoles; $i++) {
		// Ensure each field has values.
		$role_name = $_POST["role_names"][$i];
		if (empty($role_name)) {
			$_SESSION["error_message"] = "Role Name is missing";
			// Redirect to Select Template with error message
			header("Location: $errorURL");
			exit;
		}

		$name = $_POST["recipient_names"][$i];
		if (empty($name)) {
			$_SESSION["error_message"] = "Name is blank for $role_name";
			// Redirect to Select Template with error message
			header("Location: $errorURL");
			exit;
		}

		$email = $_POST["recipient_emails"][$i];
		if ($_REQUEST["document_is_embedded"] != "true" && empty($email)) {
			error_log("~~~~~GIONG to $errorURL~~~~~");
			$_SESSION["error_message"] = "Email is blank for $role_name";
			// Redirect to Select Template with error message
			header("Location: $errorURL");
			exit;
		}
		
		$roles[$role_name] = array('name' => htmlspecialchars($name), 'email' => htmlspecialchars($email), 'locked' => 'true');
	}

	if (isset($_POST["mergefield_names"])) {
		// Get number of mergefields
		$numMergeFields = count($_POST["mergefield_names"]);

		for($i=0; $i < $numMergeFields; $i++) {
			$mergefield_name = $_POST["mergefield_names"][$i];
			if (empty($mergefield_name)) {
				$_SESSION["error_message"] = "Mergefield Name is missing";
				// Redirect to Select Template with error message
				header("Location: $errorURL");
				exit;
			}

			$mergefield_value = $_POST["mergefield_values"][$i];
			$mergefields[$mergefield_name] = array('value' => htmlspecialchars($mergefield_value), 'locked' => 'true');
		}
	}

	$user_id = $_SESSION["user_id"];
	
	// Create Document in local database
	$document = new Document();
	$document->user_id = $user_id;
	$document->save();
	// Adds tag to track created document with our id and user that created document
	$tags = array('user' => $user_id, 'document' => $document->id);	

	// Callback URL with document_id in the params to help identify/authenicate the request.
	$callbackURL = "$siteURL/document_callback.php?document_id=$document->id";
	
	// Calls API to send template
	if ($_REQUEST["document_is_embedded"] == "true")
		$response = $rightsignature->sendSelfSigningDocument($guid, trim($_SESSION["document_subject"]), $roles, $mergefields, $tags, trim($_SESSION["document_description"]), $callbackURL);
	else
		$response = $rightsignature->sendTemplate($guid, trim($_SESSION["document_subject"]), $roles, $mergefields, $tags, trim($_SESSION["document_description"]), $callbackURL);
	
	// Checks status of document
	if (simplexml_load_string($response)->status == 'sent') {
		// Records Sent Document guid
		$sent_guid = simplexml_load_string($response)->guid;
		$document->rs_document_id = $sent_guid;
		
		if($document->save()) {
			if (isset($_REQUEST["document_is_embedded"]) && $_REQUEST["document_is_embedded"] == "true")
				header("Location: $siteURL/sign_document.php?guid=$sent_guid");
			else
				$message = "Your document has been sent! <br/> Your document ID is $document->id";
		} else {
			$message = "Your document has been sent, but there was a problem recording the document. 
			The problem has been reported and it might take a few days for your document to show up on your documents.";
			// TODO: Do some error handling here
		}
		
	} else {
		$_SESSION["error_message"] = "There was a problem creating the document, please restart process.";
		header("Location: $siteURL/");
	}
} else {	
	$_SESSION["error_message"] = "Template was not properly set, please restart process.";
	header("Location: $siteURL/");
}
?>
<html>
	<head>
	<?php common_styles(); ?>
	</head>
	<body><div class="container">
		<div class="span-24">
			<h2><?php echo $message; ?></h2>
		</div>
		<div class="span-24 box">
			Menu:
		</div>
		<div class="span-24">
		<ul>
			<li><a href="/">Send another document</a></li>
		<ul>
		</div>
	</div>
</body>
</html>
<?php
ob_flush();
?>
