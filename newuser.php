<?php

	/**
	* Make sure you started your'e sessions!
	* You need to include su.inc.php to make SimpleUsers Work
	* After that, create an instance of SimpleUsers and your'e all set!
	*/

	session_start();
	require_once(dirname(__FILE__)."/simpleusers/su.inc.php");

	$SimpleUsers = new SimpleUsers();

	// Validation of input
	if( isset($_POST["username"]) )
	{
		if( empty($_POST["username"]) || empty($_POST["password"]) )
			$error = "You have to choose a username and a password";
    else
    {
    	// Both fields have input - now try to create the user.
    	// If $res is (bool)false, the username is already taken.
    	// Otherwise, the user has been added, and we can redirect to some other page.
			$res = $SimpleUsers->createUser($_POST["username"], $_POST["password"]);

			if(!$res)
				$error = "Username already taken.";
			else
			{
					header("Location: users.php");
					exit;
			}
		}

	} // Validation end

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title></title>
	  <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	  <style type="text/css">

			* {	margin: 0px; padding: 0px; }
			body
			{
				padding: 30px;
				font-family: Calibri, Verdana, "Sans Serif";
				font-size: 12px;
			}
			table
			{
				width: 800px;
				margin: 0px auto;
			}

			th, td
			{
				padding: 3px;
			}

			.right
			{
				text-align: right;
			}

	  	h1
	  	{
	  		color: #FF0000;
	  		border-bottom: 2px solid #000000;
	  		margin-bottom: 15px;
	  	}

	  	p { margin: 10px 0px; }
	  	p.faded { color: #A0A0A0; }

	  </style>
	</head>
	<body>

		<h1>Register new user</h1>

		<?php if( isset($error) ): ?>
		<p>
			<?php echo $error; ?>
		</p>
		<?php endif; ?>

		<form method="post" action="">
			<p>
				<label for="username">Username:</label><br />
				<input type="text" name="username" id="username" />
			</p>

			<p>
				<label for="password">Password:</label><br />
				<input type="text" name="password" id="password" />
			</p>

			<p>
				<input type="submit" name="submit" value="Register" />
			</p>

		</form>

	</body>
</html>