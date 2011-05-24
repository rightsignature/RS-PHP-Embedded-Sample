<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/config/common.inc.php");

?>
<html>
<head>
	<title>Log Into Your Account</title>
	<?php common_styles(); ?>
</head>
<body>
	<div class="container">
		<div class="span-24 prepend-top append-bottom">
			<h3 class="bottom">Log Into Your Account</h3>
			<?php message_div() ?>
			<form action="/sessions/create_user.php" method="post">
				<div class="span-24">
					<label class="span-1">Name:</label><input type="text" name="user_name"/>
				</div>
				<div class="span-24">
					<label class="span-1">Email:</label><input type="text" name="user_email"/>
				</div>
				<div class="span-24 prepend-top">
					<span class="span-4"><input type="submit" value="Sign Up" /></span>
				</div>
			</form>
		</div>
	</div>
</body>
</html>