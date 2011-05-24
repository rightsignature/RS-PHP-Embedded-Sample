<?php
ob_start();
// This page shows an example of using taggings in RightSignature's List Documents API Call
// to filter results for current user
// 
require_once($_SERVER['DOCUMENT_ROOT'] . "/config/common.inc.php");

$user_id = $_SESSION["user_id"];

if (empty($user_id)) {
	$_SESSION['error_message'] = "You need to be logged in to see your sent documents.";
	header("Location: $siteURL/");
}

// create tags for API call with current user id
$tags = array("user" => $user_id);

// Gets page number to jump to from parameters
if (isset($_REQUEST["page"]))
	$page = $_REQUEST["page"];
else
	$page = 1;
?>

<html>
<head>
	<?php common_styles(); ?>
</head>
<body>
	<?php login_bar(); ?>
	<div class="container">
		<div class="span-24">
			<h2>Your Documents</h2>
		</div>
		<div class="span-24">
			<table>
				<?php
					// Get Documents from RightSignature filtered by given tags, with no search query , and specified page #
					$response = $rightsignature->getDocuments($tags, NULL, $page);
					$xml = simplexml_load_string($response);
					
					// Gets current page from xml
					$currentPage = (int)$xml->{'current-page'};
					// Gets total pages from xml
					$totalPages = (int)$xml->{'total-pages'};
					
					// Output each document
					foreach ($xml->documents->document as $docXML) {
						echo "<tr><td>
							<span class=\"title\">$docXML->subject</span> </br>
							<span><img src=\"" . urldecode($docXML->{'thumbnail-url'}); 
							echo "\"/></span></td>
							<td><span class=\"span-2\">$docXML->state</span></td><td>";
							foreach ($docXML->recipients->recipient as $recXML) {
								echo "<div>$recXML->name ($recXML->email) - ";
								if ($recXML->{'is-sender'} == 'true')
									echo "sender";
								else
									echo "$recXML->state signer";
								echo "</div>";
							}
							// Need to url decode URLs from XML
							if ($docXML->state == 'signed')
								echo "</td><td><span class=\"span-1\"><a href=\"" . urldecode($docXML->{'signed-pdf-url'}) . "\">Download</a></span>";
							else
								echo "</td><td>";
							echo "</td></tr>";
							
					}
				?>
			</table>
		</div>
		
	 	<div class="span-24 footer">
			<span class="left"><?php 
				if ($currentPage == 1)
					echo '<p class="quiet">Prev Page</p>';
				else
					echo "<a class=\"button\" href=\"/list_documents.php?page=" . ($currentPage - 1) . "\">Prev Page</a>";
				?>
			</span>

			<span class="right"><?php 
				if ($currentPage == $totalPages)
					echo '<p class="quiet">Next Page</p>';
				else
					echo "<a class=\"button\" href=\"/list_documents.php?page=" . ($currentPage + 1) . "\">Next Page</a>";
				?>
			</span>

			<span class="center"><p>Page <?php echo $currentPage ?> of <?php echo $totalPages ?></p></span>

		</div>
	</div>
</body>
</html>

<?php
ob_flush();
?>