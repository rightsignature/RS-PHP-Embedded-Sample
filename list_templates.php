<?php
ob_start();
// This page shows an example of using taggings in RightSignature's List Templates API Call
// to filter results for current user
// 
require_once($_SERVER['DOCUMENT_ROOT'] . "/config/common.inc.php");

$user_id = $_SESSION["user_id"];

if (empty($user_id)) {
	$_SESSION['error_message'] = "You need to be logged in to see your sent templates.";
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
			<h2>Your Templates</h2>
		</div>
		<div class="span-24">
			<table>
				<?php
					// Get Templates from RightSignature filtered by given tags, with no search query , and specified page #
					$response = $rightsignature->getTemplates($tags, NULL, $page);
					$xml = simplexml_load_string($response);
					
					// Gets current page from xml
					$currentPage = (int)$xml->{'current-page'};
					// Gets total pages from xml
					$totalPages = (int)$xml->{'total-pages'};
					
					// Output each document
					foreach ($xml->templates->template as $tempXML) {
						echo "<tr><td>
							<span class=\"title\">$tempXML->subject</span> </br>
							<span><img src=\"" . urldecode($tempXML->{'thumbnail-url'}); 
						echo "\"/></span></td>
						<td><span class=\"span-12\">Subject: $tempXML->subject</span>";
						echo "<span class=\"span-12\">Message: <blockquote>$tempXML->message</blockquote> </span>";
						echo "<span class=\"span-12\">filename: $tempXML->filename </span>";
						echo "<span class=\"span-12\">Pages: " . $tempXML->{'page-count'} . "</span>";
						echo "<span class=\"span-12\">created: " . $tempXML->{'created-at'} . " </span>";
						echo "</td><td>";
						
						echo "</td><td><form action=\"/select_template.php\" method=\"post\">
						<input type=\"hidden\" name=\"guid\" value=\"$tempXML->guid\" /> <input type=\"submit\" value=\"Send Document Using this Template\" />
						</form></td></tr>";
							
					}
				?>
			</table>
		</div>
		
	 	<div class="span-24 footer">
			<span class="left"><?php 
				if ($currentPage == 1)
					echo '<p class="quiet">Prev Page</p>';
				else
					echo "<a class=\"button\" href=\"/list_templates.php?page=" . ($currentPage - 1) . "\">Prev Page</a>";
				?>
			</span>

			<span class="right"><?php 
				if ($currentPage == $totalPages)
					echo '<p class="quiet">Next Page</p>';
				else
					echo "<a class=\"button\" href=\"/list_templates.php?page=" . ($currentPage + 1) . "\">Next Page</a>";
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