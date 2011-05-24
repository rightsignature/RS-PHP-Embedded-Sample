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
			<form action="/sessions/login_user.php" method="post">
				</div>
				<div class="span-24">
					<label class="span-1">Email:</label><input type="text" name="user_email"/>
				</div>
				<div class="span-24 prepend-top">
					<span class="span-4"><input type="submit" value="Log In" /></span>
					<span class="span-5"><a href="/signup.php">Sign Up for an Account</a></span>
				</div>
			</form>
		</div>
	</div>
</body>
</html>