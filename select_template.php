<?php
require_once("config/common.inc.php");

// Gets Template details from XML
$response = $rightsignature->getTemplateDetails(htmlspecialchars($_REQUEST['guid']));
// Parses xml
$template_xml = simplexml_load_string($response);

// Saves Template guid
$_SESSION["current_template"] = htmlspecialchars($_REQUEST['guid']);

// Needs to URL decode the XML's thumbnail URL
$thumbnailURL = urldecode($template_xml->{'thumbnail-url'});

$roles = $template_xml->roles->role;
$mergefields = $template_xml->{'merge-fields'}->{'merge-field'};
$subject = $template_xml->subject;

$template_roles = array();

?>

<html>
<head>
	<?php common_styles(); ?>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js"></script>
	<title>Send Your Document for Signatures</title>
</head>
<body>
	<?php login_bar(); ?>
	<div class="container push-1">
		<div class="span-23 title">
			<h2 class="bottom">Complete the Form</h2>
		</div>
		<div class="push-1 span-23 top description append-bottom">
			Fill in the Information below and click 'Send' to send your document for signatures.
		</div>
			<?php message_div(); ?>
		<div class="span-23 title">
			<h2 class="bottom"><?php echo $template_xml->subject ?></h3>
			<h5 class="quiet top bottom"><?php echo $template_xml->filename ?></h5>
		</div>
		<div class="span-23"><img src="<?php echo $thumbnailURL; ?>" /></div>
		<div class="span-23 prepend-top append-bottom"></div>
		<div class="span-23 append-bottom">
			<h3 class="bottom">People Involved</h3>
			<h5 class="top quiet">Enter the names and email addresses for the following people</h5>
		<form action="send_template.php" method="POST">
			<label>Sign Now?</label><input type="checkbox" name="document_is_embedded" value="true" onclick="toggleEmailVisibility();"/>
			<?php 
				foreach($roles as $role) {
					echo '<div class="span-23 bottom">';
					echo "<span class=\"span-3 bottom box\">$role->name:</span> 
								<span class=\"span-19 last\">
									<span class=\"span-19 bottom\">
										<span class=\"quiet bottom span-4 small\">
											<label>Name</label>
										</span>
										<span class=\"quiet bottom span-15 small last recipient_emails\">
											<label>Email Address</label>
										</span>
									</span>
									<span class=\"span-19 top\">
										<span class=\"quiet top span-4\">
											<input type='text' name='recipient_names[]' />
										</span>
										<span class=\"quiet top span-4\">
											<input type='text' class=\"recipient_emails\" name='recipient_emails[]' />
											<input type=\"hidden\" name=\"role_names[]\" value=\"$role->name\" />	
										</span>
									</span>
								</span>";
					echo '</div>';
				}
			?>
			</div>
			
			<div class="span-23 prepend-top append-bottom">
				<h3 class="bottom">Document Information</h3>
				<h5 class="top quiet">These field will be visible to the signers of the document</h5>
				<div class="span-23">
					<div class="span-23 bottom">
						<label class="quiet top bottom">Title:</label>
					</div>
					<div class="span-23 top">
						<input type="text" name="document_subject" value="<?php echo htmlspecialchars($template_xml->subject) ?>" size="55" />
					</div>
					<div class="span-23 bottom">
						<label class="quiet top bottom">Description:</label>
					</div>
					<div class="span-23 top">
						<textarea name="document_description">
							<?php echo htmlspecialchars($template_xml->message) ?>
						</textarea>
					</div>
				</div>
			</div>
			
			<?php
				if (!empty($mergefields)) {
					echo '<div class="span-23 prepend-top append-bottom"><h3 class="bottom">Optional Information</h3>';
					echo '<h5 class="top quiet">Enter additional information for the following fields</h5>';
						foreach($mergefields as $mergefield) {
							echo '<div class="span-23 bottom">';
							echo "<span class=\"span-3 bottom box\"><label>$mergefield->name:</label></span> 
										<span class=\"span-19 bottom last\">
											<textarea style=\"width:316px; height:40px;\" name='mergefield_values[]'></textarea>
											<input type=\"hidden\" name=\"mergefield_names[]\" value=\"$mergefield->name\" />
										</span>";
							echo '</div>';
						}
					echo '</div>';
				}
				?>
				<input type="submit" value="Send">
			</form>
		</div>
	</div>
	
	<script type="text/javascript">
		onload=function(){
			if (document.getElementsByClassName == undefined) {
				document.getElementsByClassName = function(className)
				{
					var hasClassName = new RegExp("(?:^|\\s)" + className + "(?:$|\\s)");
					var allElements = document.getElementsByTagName("*");
					var results = [];

					var element;
					for (var i = 0; (element = allElements[i]) != null; i++) {
						var elementClass = element.className;
						if (elementClass && elementClass.indexOf(className) != -1 && hasClassName.test(elementClass))
							results.push(element);
					}

					return results;
				}
			}
		}
		
		function toggleEmailVisibility() {
			$('.recipient_emails').toggle();
		}
	</script>
</body>
</html>