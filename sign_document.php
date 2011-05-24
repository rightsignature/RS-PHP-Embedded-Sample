<?php
// This page shows an example of using RightSignature's Embedded Signing through the API
// to filter results for current user
// 
ob_start();
require_once($_SERVER['DOCUMENT_ROOT'] . "/config/common.inc.php");

// Width of Widget, CANNOT BE CHANGED and it cannot be dynamic. 706 is optimal size
$widgetWidth=706;

// Height of widget is changeable (Optional)
$widgetHeight=500;

$guid = htmlspecialchars($_REQUEST["guid"]);
if (empty($guid)) {
	$_SESSION["error_message"] = "Cannot find document with given GUID.";
	header("Location: $siteURL/list_documents.php");
}

// Gets signer link and reload this page after each successfuly signature to refresh the signer-links list
$response = $rightsignature->getSingerLinks($guid, "$siteURL/sign_document.php?guid=$guid");
$xml = simplexml_load_string($response);

// Checks xml for error->message node
$alreadySigned = false;
$error = false;
if (isset($xml->message)) {
	$error = true;
	if ($xml->message == "Document is already signed.")
		$alreadySigned = true;
}
?>

<html>
<head>
	<?php common_styles(); ?>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js"></script>
</head>
<body>
	<?php if ($error)
		login_bar();
	?>
	<div class="container">
		<div class="span-24 bottom">
			<h2 class="bottom">Please Sign the document</h2>
		</div>
		<div class="span-24 top">
			<ol>
				<li> Select the Person to sign </li>
				<li> Fill in the require information </li>
				<li> Sign the Document with your mouse and click 'Submit'. </li>
			</ol>
		</div>
		<div class="span-24">
			<?php
			if ($error) {
				if ($alreadySigned) {
					echo "<h3>Document is Signed</h3><div class=\"span-24\">";
					echo "All Signers have Successfully Signed. <a href=\"/list_documents.php\">Click here to see your sent documents.</a>";
					echo "</div>";
				} else {
					echo "<h3>Error</h3><div class=\"span-24\">";
					echo "Error:" . $xml->message;
					echo "</div>";
				}
			} else {
				foreach ($xml->{'signer-links'}->{'signer-link'} as $signer) {
					echo "<h3>Select Signer</h3><div class=\"span-24\">";
					echo "<span class=\"span-5\">Name: " . $signer->name . "</span>";
					echo "<span class=\"span-8\"><a href=\"#\" onclick=\"change_signer('$rightsignature->base_url/signatures/embedded?height=$widgetHeight&rt=" . $signer->{'signer-token'} . "')\">Sign Document</a></span>";
					echo "</div>";
				}
			}
			?>
			<div class="span-24">
				<iframe width="<?php echo $widgetWidth; ?>px" scrolling="no" height="<?php echo $widgetHeight; ?>px" frameborder="0" id="signing-widget"></iframe>
			</div>
		</div>
	</div>
	
	<script type="text/javascript">
		function change_signer(url) {
			$("#signing-widget").attr('src', url)
			;
		}
	</script>
</body>
</html>


<?php
ob_flush();
?>