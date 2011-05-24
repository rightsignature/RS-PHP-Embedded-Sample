<?php
require_once("config/common.inc.php");

?>
<html>
<head>
	<title>Template Creation</title>
	<?php common_styles(); ?>
</head>
<body>
	<?php login_bar(); ?>
	<div class="container">
		<div class="span-24 prepend-top append-bottom">
			<h3 class="bottom">Select premade Template</h3>
			<?php message_div() ?>
			<?php
				if (empty($_SESSION["user_id"])) {
					echo '<div class="span-24 title">You need to be logged in to Be able to create an account.</div>';
				} else {
					// Calls API to get templates XML and parses it
					$templates_xml = simplexml_load_string($rightsignature->getTemplates());
					echo '
					<form action="select_template.php" method="post">
						<select name="guid">';
							// Loops through each template in XML and creates dropbox
							foreach($templates_xml->templates->template as $template) {
								// Split up tags string into an array
								$tags = preg_split("/,/",(string)$template->tags);
								
								// If tags has a 'user' tag with the same value as the current user, 
								// OR  there is no 'user' tag in the tags
								if (in_array('user:' . $_SESSION["user_id"], $tags) || 
										!preg_match('/\buser:[0-9]+\b/', (string)$template->tags)) {
									echo "<option value=\"$template->guid\">";
									echo htmlspecialchars($template->subject);
									echo '</option>';
								} else {
									continue;
								}
							}	
						echo '</select>
						<div class="span-24 prepend-top">
							<span class="span-4"><input type="submit" value="Send Document" /></span>
							<span class="span-5"><a href="/new_template.php">Upload New Template</a></span>
						</div>
					</form>';
				}
			?>
		</div>
	</div>
</body>
</html>